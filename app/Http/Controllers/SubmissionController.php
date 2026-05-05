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
        $submission = null;
        if ($id = session('submitted_id')) {
            $submission = Submission::find($id);
        }
        return view('form', compact('submission'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom_complet'         => 'required|string|max:255',
            'situation_familiale' => 'required|string',
        ]);

        $folder = 'submissions/' . Str::slug($request->nom_complet) . '_' . time();

        $storeFile = function ($file, $name) use ($folder) {
            if (!$file) return null;
            $ext  = $file->getClientOriginalExtension();
            return $file->storeAs($folder, $name . '.' . $ext, 'public');
        };

        // Employé
        $ci_employe    = $storeFile($request->file('ci_employe'),    'ci_employe');
        $photo_employe = $storeFile($request->file('photo_employe'), 'photo_employe');

        // Père
        $ci_pere    = $storeFile($request->file('ci_pere'),    'ci_pere');
        $photo_pere = $storeFile($request->file('photo_pere'), 'photo_pere');

        // Mère
        $ci_mere    = $storeFile($request->file('ci_mere'),    'ci_mere');
        $photo_mere = $storeFile($request->file('photo_mere'), 'photo_mere');

        // Conjoint(e)
        $ci_conjoint    = $storeFile($request->file('ci_conjoint'),    'ci_conjoint');
        $photo_conjoint = $storeFile($request->file('photo_conjoint'), 'photo_conjoint');

        // Fratrie combinée (frères + sœurs) avec nom et type
        $freres = [];
        $soeurs = [];
        $count  = (int) $request->input('fratrie_count', 0);
        for ($i = 0; $i < $count; $i++) {
            $item = [
                'nom'   => $request->input("fratrie_nom_$i"),
                'ci'    => $storeFile($request->file("fratrie_ci_$i"),    "fratrie_{$i}_ci"),
                'photo' => $storeFile($request->file("fratrie_photo_$i"), "fratrie_{$i}_photo"),
            ];
            if ($request->input("fratrie_type_$i") === 'soeur') {
                $soeurs[] = $item;
            } else {
                $freres[] = $item;
            }
        }

        // Descendants avec nom
        $descendants = [];
        $dcount = (int) $request->input('descendants_count', 0);
        for ($i = 0; $i < $dcount; $i++) {
            $descendants[] = [
                'nom'   => $request->input("descendant_nom_$i"),
                'ci'    => $storeFile($request->file("descendant_ci_$i"),    "descendant_{$i}_ci"),
                'photo' => $storeFile($request->file("descendant_photo_$i"), "descendant_{$i}_photo"),
            ];
        }

        $submission = Submission::create([
            'nom_complet'         => $request->nom_complet,
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
        ]);

        session(['submitted_id' => $submission->id]);

        return response()->json([
            'success'       => true,
            'message'       => 'Fiche soumise avec succès !',
            'submission_id' => $submission->id,
        ]);
    }

    public function resetSession()
    {
        session()->forget('submitted_id');
        return redirect()->route('form');
    }

    // Per-submission Excel download (session-protected)
    public function download(Submission $submission)
    {
        if (session('submitted_id') !== $submission->id) {
            abort(403, 'Accès non autorisé.');
        }

        $writer   = new Xlsx($this->buildSpreadsheet(collect([$submission])));
        $filename = 'fiche_' . Str::slug($submission->nom_complet) . '_' . $submission->created_at->format('Ymd') . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    // Admin dashboard
    public function index()
    {
        $submissions = Submission::latest()->get();
        return view('admin.index', compact('submissions'));
    }

    // Admin – single submission
    public function show(Submission $submission)
    {
        return view('admin.show', compact('submission'));
    }

    // Admin – export all as Excel
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

    // ── Shared spreadsheet builder ────────────────────────────────────────────
    private function buildSpreadsheet(\Illuminate\Support\Collection $submissions): Spreadsheet
    {
        $baseUrl = config('app.url');

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
            string  $type,
            ?string $nom,
            ?string $situation,
            ?string $ciPath,
            ?string $photoPath,
            ?string $date,
            string  $bgColor
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
                $sheet->getStyle($c)->applyFromArray([
                    'font' => ['color' => ['rgb' => '1155CC'], 'underline' => Font::UNDERLINE_SINGLE],
                ]);
            }
            if ($photoPath) {
                $c = $coord(5, $row);
                $sheet->setCellValue($c, 'Voir Photo');
                $sheet->getCell($c)->setHyperlink(new Hyperlink($baseUrl . '/storage/' . $photoPath, 'Voir Photo'));
                $sheet->getStyle($c)->applyFromArray([
                    'font' => ['color' => ['rgb' => '1155CC'], 'underline' => Font::UNDERLINE_SINGLE],
                ]);
            }
            $row++;
        };

        $colors = [
            'employe'    => 'DDEEFF',
            'pere'       => 'FFF9E6',
            'mere'       => 'FFF9E6',
            'conjoint'   => 'E8F5E9',
            'descendant' => 'F3E5F5',
            'sibling'    => 'FFF3E0',
        ];

        foreach ($submissions as $s) {
            $date = $s->created_at->format('d/m/Y H:i');

            $writeRow('Employé', $s->nom_complet, $s->situation_familiale,
                      $s->ci_employe, $s->photo_employe, $date, $colors['employe']);

            if ($s->nom_pere || $s->ci_pere || $s->photo_pere) {
                $writeRow('Père', $s->nom_pere, null,
                          $s->ci_pere, $s->photo_pere, null, $colors['pere']);
            }

            if ($s->nom_mere || $s->ci_mere || $s->photo_mere) {
                $writeRow('Mère', $s->nom_mere, null,
                          $s->ci_mere, $s->photo_mere, null, $colors['mere']);
            }

            if ($s->nom_conjoint || $s->ci_conjoint || $s->photo_conjoint) {
                $writeRow('Conjoint(e)', $s->nom_conjoint, null,
                          $s->ci_conjoint, $s->photo_conjoint, null, $colors['conjoint']);
            }

            foreach ($s->descendants ?? [] as $i => $d) {
                $writeRow('Descendant ' . ($i + 1), $d['nom'] ?? null, null,
                          $d['ci'] ?? null, $d['photo'] ?? null, null, $colors['descendant']);
            }

            foreach ($s->freres ?? [] as $i => $f) {
                $writeRow('Frère ' . ($i + 1), $f['nom'] ?? null, null,
                          $f['ci'] ?? null, $f['photo'] ?? null, null, $colors['sibling']);
            }

            foreach ($s->soeurs ?? [] as $i => $sr) {
                $writeRow('Sœur ' . ($i + 1), $sr['nom'] ?? null, null,
                          $sr['ci'] ?? null, $sr['photo'] ?? null, null, $colors['sibling']);
            }

            $row++; // blank separator
        }

        foreach (range(1, 6) as $colIndex) {
            $letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
            $sheet->getColumnDimension($letter)->setAutoSize(true);
        }

        return $spreadsheet;
    }
}
