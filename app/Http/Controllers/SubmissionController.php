<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Hyperlink;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;

class SubmissionController extends Controller
{
    public function form()
    {
        $phoneVerified = session('phone_verified', false);
        $verifiedPhone = session('verified_phone');
        $existing      = null;

        if ($phoneVerified && $verifiedPhone) {
            $existing = Submission::where('phone', $verifiedPhone)->latest()->first();
            if ($existing) {
                session(['submitted_id' => $existing->id]);
            }
        }

        return view('form', compact('phoneVerified', 'verifiedPhone', 'existing'));
    }

    public function store(Request $request)
    {
        $fileMimes = 'nullable|file|mimes:jpg,jpeg,pdf|max:10240';
        $rules = [
            'nom_complet'         => 'required|string|max:255',
            'situation_familiale' => 'required|string',
            'ci_employe'    => $fileMimes, 'photo_employe' => $fileMimes,
            'ci_pere'       => $fileMimes, 'photo_pere'    => $fileMimes,
            'ci_mere'       => $fileMimes, 'photo_mere'    => $fileMimes,
            'ci_conjoint'   => $fileMimes, 'photo_conjoint'=> $fileMimes,
        ];
        for ($i = 0; $i < (int) $request->input('fratrie_count', 0); $i++) {
            $rules["fratrie_ci_$i"] = $fileMimes; $rules["fratrie_photo_$i"] = $fileMimes;
        }
        for ($i = 0; $i < (int) $request->input('descendants_count', 0); $i++) {
            $rules["descendant_ci_$i"] = $fileMimes; $rules["descendant_photo_$i"] = $fileMimes;
        }
        $request->validate($rules);

        // Determine if this is an update
        $existing = session('submitted_id') ? Submission::find(session('submitted_id')) : null;

        // Reuse existing folder or create new one
        if ($existing) {
            $anyPath = $existing->ci_employe ?? $existing->photo_employe
                    ?? $existing->ci_pere    ?? $existing->ci_mere;
            $folder  = $anyPath
                ? dirname($anyPath)
                : 'submissions/' . Str::slug($request->nom_complet) . '_' . $existing->created_at->timestamp;
        } else {
            $folder = 'submissions/' . Str::slug($request->nom_complet) . '_' . time();
        }

        // Store new file or fall back to old path
        $storeFile = function ($file, $name, $oldPath = null) use ($folder) {
            if (!$file) return $oldPath;
            $ext = $file->getClientOriginalExtension();
            return $file->storeAs($folder, $name . '.' . $ext, 'public');
        };

        $ci_employe    = $storeFile($request->file('ci_employe'),    'ci_employe',    $existing?->ci_employe);
        $photo_employe = $storeFile($request->file('photo_employe'), 'photo_employe', $existing?->photo_employe);
        $ci_pere       = $storeFile($request->file('ci_pere'),       'ci_pere',       $existing?->ci_pere);
        $photo_pere    = $storeFile($request->file('photo_pere'),    'photo_pere',    $existing?->photo_pere);
        $ci_mere       = $storeFile($request->file('ci_mere'),       'ci_mere',       $existing?->ci_mere);
        $photo_mere    = $storeFile($request->file('photo_mere'),    'photo_mere',    $existing?->photo_mere);
        $ci_conjoint   = $storeFile($request->file('ci_conjoint'),   'ci_conjoint',   $existing?->ci_conjoint);
        $photo_conjoint= $storeFile($request->file('photo_conjoint'),'photo_conjoint',$existing?->photo_conjoint);

        // Fratrie (combined) – preserve old file paths via hidden inputs
        $freres = [];
        $soeurs = [];
        $count  = (int) $request->input('fratrie_count', 0);
        for ($i = 0; $i < $count; $i++) {
            $item = [
                'nom'   => $request->input("fratrie_nom_$i"),
                'ci'    => $storeFile($request->file("fratrie_ci_$i"),    "fratrie_{$i}_ci",    $request->input("fratrie_ci_old_$i")),
                'photo' => $storeFile($request->file("fratrie_photo_$i"), "fratrie_{$i}_photo", $request->input("fratrie_photo_old_$i")),
            ];
            if ($request->input("fratrie_type_$i") === 'soeur') {
                $soeurs[] = $item;
            } else {
                $freres[] = $item;
            }
        }

        // Descendants – preserve old file paths via hidden inputs
        $descendants = [];
        $dcount = (int) $request->input('descendants_count', 0);
        for ($i = 0; $i < $dcount; $i++) {
            $descendants[] = [
                'nom'   => $request->input("descendant_nom_$i"),
                'ci'    => $storeFile($request->file("descendant_ci_$i"),    "descendant_{$i}_ci",    $request->input("descendant_ci_old_$i")),
                'photo' => $storeFile($request->file("descendant_photo_$i"), "descendant_{$i}_photo", $request->input("descendant_photo_old_$i")),
            ];
        }

        $data = [
            'nom_complet'         => $request->nom_complet,
            'phone'               => session('verified_phone'),
            'situation_familiale' => $request->situation_familiale,
            'ci_employe'          => $ci_employe,
            'photo_employe'       => $photo_employe,
            'nom_pere'            => $request->nom_pere,
            'ci_pere'             => $ci_pere,
            'photo_pere'          => $photo_pere,
            'nom_mere'            => $request->nom_mere,
            'ci_mere'             => $ci_mere,
            'photo_mere'          => $photo_mere,
            'freres'              => $freres,
            'soeurs'              => $soeurs,
            'nom_conjoint'        => $request->nom_conjoint,
            'ci_conjoint'         => $ci_conjoint,
            'photo_conjoint'      => $photo_conjoint,
            'descendants'         => $descendants,
        ];

        if ($existing) {
            $existing->update($data);
            $submission = $existing;
        } else {
            $submission = Submission::create($data);
        }

        session(['submitted_id' => $submission->id]);

        return response()->json([
            'success'       => true,
            'updated'       => (bool) $existing,
            'submission_id' => $submission->id,
        ]);
    }

    public function resetSession()
    {
        session()->forget(['submitted_id', 'phone_verified', 'verified_phone', 'otp_phone']);
        return redirect()->route('form');
    }

    public function download(Submission $submission)
    {
        if (session('submitted_id') !== $submission->id) {
            abort(403);
        }

        $writer   = new Xlsx($this->buildSpreadsheet(collect([$submission])));
        $filename = 'fiche_' . Str::slug($submission->nom_complet) . '_' . $submission->created_at->format('Ymd') . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function adminDownload(Submission $submission)
    {
        $writer   = new Xlsx($this->buildSpreadsheet(collect([$submission])));
        $filename = 'fiche_' . Str::slug($submission->nom_complet) . '_' . $submission->created_at->format('Ymd') . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function index()
    {
        $submissions = Submission::latest()->get();
        return view('admin.index', compact('submissions'));
    }

    public function show(Submission $submission)
    {
        return view('admin.show', compact('submission'));
    }

    public function exportExcel()
    {
        $submissions = Submission::latest()->get();
        $writer      = new Xlsx($this->buildSpreadsheet($submissions));
        $filename    = 'cnass_submissions_' . date('Y-m-d') . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function buildSpreadsheet(\Illuminate\Support\Collection $submissions): Spreadsheet
    {
        $baseUrl     = config('app.url');
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Soumissions');

        $colHeaders = ['Type', 'Nom complet', 'Situation familiale', 'CI', 'Photo', 'Date soumission'];
        $sheet->fromArray($colHeaders, null, 'A1');
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1A3A6E']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->freezePane('A2');

        $coord = fn(int $c, int $r): string =>
            \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c) . $r;

        $row = 2;

        $writeRow = function (
            string $type, ?string $nom, ?string $situation,
            ?string $ciPath, ?string $photoPath, ?string $date, string $bgColor
        ) use ($sheet, &$row, $coord, $baseUrl) {
            $sheet->fromArray([$type, $nom ?? '', $situation ?? '', '', '', $date ?? ''], null, "A{$row}");
            $sheet->getStyle("A{$row}:F{$row}")->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
            ]);
            $sheet->getStyle("A{$row}")->getFont()->setBold(true);

            if ($ciPath) {
                $c = $coord(4, $row);
                $sheet->setCellValue($c, 'Voir CI');
                $sheet->getCell($c)->setHyperlink(new Hyperlink($baseUrl . '/storage/' . $ciPath, 'Voir CI'));
                $sheet->getStyle($c)->applyFromArray(['font' => ['color' => ['rgb' => '1155CC'], 'underline' => Font::UNDERLINE_SINGLE]]);
            }
            if ($photoPath) {
                $c = $coord(5, $row);
                $sheet->setCellValue($c, 'Voir Photo');
                $sheet->getCell($c)->setHyperlink(new Hyperlink($baseUrl . '/storage/' . $photoPath, 'Voir Photo'));
                $sheet->getStyle($c)->applyFromArray(['font' => ['color' => ['rgb' => '1155CC'], 'underline' => Font::UNDERLINE_SINGLE]]);
            }
            $row++;
        };

        $colors = [
            'employe' => 'DDEEFF', 'pere' => 'FFF9E6', 'mere' => 'FFF9E6',
            'conjoint' => 'E8F5E9', 'descendant' => 'F3E5F5', 'sibling' => 'FFF3E0',
        ];

        foreach ($submissions as $s) {
            $date = $s->created_at->format('d/m/Y H:i');
            $writeRow('Employé',    $s->nom_complet,  $s->situation_familiale, $s->ci_employe,  $s->photo_employe,  $date, $colors['employe']);
            if ($s->nom_pere   || $s->ci_pere   || $s->photo_pere)   $writeRow('Père',       $s->nom_pere,    null, $s->ci_pere,    $s->photo_pere,    null, $colors['pere']);
            if ($s->nom_mere   || $s->ci_mere   || $s->photo_mere)   $writeRow('Mère',       $s->nom_mere,    null, $s->ci_mere,    $s->photo_mere,    null, $colors['mere']);
            if ($s->nom_conjoint||$s->ci_conjoint||$s->photo_conjoint)$writeRow('Conjoint(e)',$s->nom_conjoint,null,$s->ci_conjoint,$s->photo_conjoint,null, $colors['conjoint']);
            foreach ($s->descendants ?? [] as $i => $d) $writeRow('Descendant '.($i+1), $d['nom']??null, null, $d['ci']??null, $d['photo']??null, null, $colors['descendant']);
            foreach ($s->freres    ?? [] as $i => $f) $writeRow('Frère '.($i+1),       $f['nom']??null, null, $f['ci']??null, $f['photo']??null, null, $colors['sibling']);
            foreach ($s->soeurs    ?? [] as $i => $sr)$writeRow('Sœur '.($i+1),        $sr['nom']??null,null, $sr['ci']??null,$sr['photo']??null,null, $colors['sibling']);
            $row++;
        }

        foreach (range(1, 6) as $colIndex) {
            $sheet->getColumnDimension(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex))->setAutoSize(true);
        }

        return $spreadsheet;
    }
}
