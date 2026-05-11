<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\Hyperlink;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class SubmissionController extends Controller
{
    /**
     * Upload disk. Files are served only through serveFile() which enforces
     * auth, so the production deployment must NOT run `php artisan storage:link`
     * (which would expose them at /storage/...).
     */
    private const DISK = 'public';

    /** Upper bounds for dynamic family-member arrays. */
    private const MAX_FRATRIE     = 30;
    private const MAX_DESCENDANTS = 30;

    /** Max file size in kilobytes (10 MB). */
    private const MAX_FILE_KB = 10240;

    public function form(Request $request)
    {
        $phoneVerified = (bool) $request->session()->get('phone_verified', false);
        $verifiedPhone = $request->session()->get('verified_phone');
        $existing      = null;

        if ($phoneVerified && $verifiedPhone) {
            $existing = Submission::where('phone', $verifiedPhone)->latest()->first();
            if ($existing) {
                $request->session()->put('submitted_id', $existing->id);
            }
        }

        return view('form', compact('phoneVerified', 'verifiedPhone', 'existing'));
    }

    public function store(Request $request)
    {
        if (!$request->session()->get('phone_verified')) {
            return response()->json(['success' => false, 'message' => 'Vérification requise.'], 403);
        }

        $fileRule = ['nullable', 'file', 'mimes:jpg,jpeg,pdf', 'mimetypes:image/jpeg,application/pdf', 'max:' . self::MAX_FILE_KB];

        $rules = [
            'nom_complet'         => ['required', 'string', 'max:255'],
            'situation_familiale' => ['required', 'string', 'in:célibataire,marié(e),divorcé(e),veuf/veuve'],
            'nom_pere'            => ['nullable', 'string', 'max:255'],
            'nom_mere'            => ['nullable', 'string', 'max:255'],
            'nom_conjoint'        => ['nullable', 'string', 'max:255'],
            'fratrie_count'       => ['nullable', 'integer', 'min:0', 'max:' . self::MAX_FRATRIE],
            'descendants_count'   => ['nullable', 'integer', 'min:0', 'max:' . self::MAX_DESCENDANTS],
            'ci_employe'    => $fileRule, 'photo_employe' => $fileRule,
            'ci_pere'       => $fileRule, 'photo_pere'    => $fileRule,
            'ci_mere'       => $fileRule, 'photo_mere'    => $fileRule,
            'ci_conjoint'   => $fileRule, 'photo_conjoint'=> $fileRule,
        ];

        $fratrieCount     = min((int) $request->input('fratrie_count', 0), self::MAX_FRATRIE);
        $descendantsCount = min((int) $request->input('descendants_count', 0), self::MAX_DESCENDANTS);

        for ($i = 0; $i < $fratrieCount; $i++) {
            $rules["fratrie_nom_$i"]   = ['nullable', 'string', 'max:255'];
            $rules["fratrie_type_$i"]  = ['nullable', 'string', 'in:frere,soeur'];
            $rules["fratrie_ci_$i"]    = $fileRule;
            $rules["fratrie_photo_$i"] = $fileRule;
        }
        for ($i = 0; $i < $descendantsCount; $i++) {
            $rules["descendant_nom_$i"]   = ['nullable', 'string', 'max:255'];
            $rules["descendant_ci_$i"]    = $fileRule;
            $rules["descendant_photo_$i"] = $fileRule;
        }

        $request->validate($rules);

        $existing = $request->session()->get('submitted_id')
            ? Submission::find($request->session()->get('submitted_id'))
            : null;

        if ($existing && $existing->phone !== $request->session()->get('verified_phone')) {
            // Session refers to a submission that doesn't belong to the verified phone.
            $existing = null;
        }

        if ($existing) {
            $anyPath = $existing->ci_employe ?? $existing->photo_employe
                    ?? $existing->ci_pere    ?? $existing->ci_mere;
            $folder  = $anyPath
                ? dirname($anyPath)
                : 'submissions/' . Str::slug($request->input('nom_complet')) . '_' . $existing->created_at->timestamp;
        } else {
            $folder = 'submissions/' . Str::slug($request->input('nom_complet')) . '_' . time() . '_' . Str::random(6);
        }

        $storeFile = function ($file, string $name, ?string $oldPath = null) use ($folder): ?string {
            if (!$file) {
                return $this->isOwnedPath($oldPath, $folder) ? $oldPath : null;
            }
            $ext = strtolower($file->getClientOriginalExtension() ?: $file->extension());
            return $file->storeAs($folder, $name . '.' . $ext, self::DISK);
        };

        $ci_employe    = $storeFile($request->file('ci_employe'),    'ci_employe',    $existing?->ci_employe);
        $photo_employe = $storeFile($request->file('photo_employe'), 'photo_employe', $existing?->photo_employe);
        $ci_pere       = $storeFile($request->file('ci_pere'),       'ci_pere',       $existing?->ci_pere);
        $photo_pere    = $storeFile($request->file('photo_pere'),    'photo_pere',    $existing?->photo_pere);
        $ci_mere       = $storeFile($request->file('ci_mere'),       'ci_mere',       $existing?->ci_mere);
        $photo_mere    = $storeFile($request->file('photo_mere'),    'photo_mere',    $existing?->photo_mere);
        $ci_conjoint   = $storeFile($request->file('ci_conjoint'),   'ci_conjoint',   $existing?->ci_conjoint);
        $photo_conjoint= $storeFile($request->file('photo_conjoint'),'photo_conjoint',$existing?->photo_conjoint);

        $freres = [];
        $soeurs = [];
        for ($i = 0; $i < $fratrieCount; $i++) {
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

        $descendants = [];
        for ($i = 0; $i < $descendantsCount; $i++) {
            $descendants[] = [
                'nom'   => $request->input("descendant_nom_$i"),
                'ci'    => $storeFile($request->file("descendant_ci_$i"),    "descendant_{$i}_ci",    $request->input("descendant_ci_old_$i")),
                'photo' => $storeFile($request->file("descendant_photo_$i"), "descendant_{$i}_photo", $request->input("descendant_photo_old_$i")),
            ];
        }

        $data = [
            'nom_complet'         => $request->input('nom_complet'),
            'phone'               => $request->session()->get('verified_phone'),
            'situation_familiale' => $request->input('situation_familiale'),
            'ci_employe'          => $ci_employe,
            'photo_employe'       => $photo_employe,
            'nom_pere'            => $request->input('nom_pere'),
            'ci_pere'             => $ci_pere,
            'photo_pere'          => $photo_pere,
            'nom_mere'            => $request->input('nom_mere'),
            'ci_mere'             => $ci_mere,
            'photo_mere'          => $photo_mere,
            'freres'              => $freres,
            'soeurs'              => $soeurs,
            'nom_conjoint'        => $request->input('nom_conjoint'),
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

        $request->session()->put('submitted_id', $submission->id);

        Log::info('Submission saved', [
            'id'      => $submission->id,
            'updated' => (bool) $existing,
            'phone'   => $submission->phone,
        ]);

        return response()->json([
            'success'       => true,
            'updated'       => (bool) $existing,
            'submission_id' => $submission->id,
        ]);
    }

    public function resetSession(Request $request)
    {
        $request->session()->forget([
            'submitted_id', 'phone_verified', 'verified_phone',
            'otp_phone', 'otp_code', 'otp_expires_at', 'otp_attempts',
        ]);
        return redirect()->route('form');
    }

    public function download(Request $request, Submission $submission)
    {
        if ($request->session()->get('submitted_id') !== $submission->id) {
            abort(403);
        }
        return $this->streamSubmissionXlsx($submission);
    }

    public function adminDownload(Submission $submission)
    {
        return $this->streamSubmissionXlsx($submission);
    }

    public function index()
    {
        $submissions = Submission::latest()->paginate(50);
        return view('admin.index', compact('submissions'));
    }

    /**
     * Hidden admin page (no nav link) — lists every phone that received an
     * OTP, showing whether they verified and whether they submitted.
     */
    public function users()
    {
        $submittedPhones = Submission::whereNotNull('phone')
            ->pluck('nom_complet', 'phone');

        $employees = Employee::orderByDesc('last_sent_at')->paginate(100);

        $totals = [
            'all'           => Employee::count(),
            'verified'      => Employee::whereNotNull('verified_at')->count(),
            'submitted'     => Employee::whereIn('phone', $submittedPhones->keys())->count(),
            'not_submitted' => Employee::whereNotNull('verified_at')
                ->whereNotIn('phone', $submittedPhones->keys())
                ->count(),
        ];

        return view('admin.users', compact('employees', 'submittedPhones', 'totals'));
    }

    public function show(Submission $submission)
    {
        return view('admin.show', compact('submission'));
    }

    public function exportExcel()
    {
        $submissions = Submission::latest()->get();
        $writer      = new Xlsx($this->buildSpreadsheet($submissions));
        $filename    = 'cnass_submissions_' . date('Y-m-d_His') . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Serve a submission file. Admins may access any file; the submission's
     * owner may access their own files via the session-tracked submitted_id.
     */
    public function serveFile(Request $request, Submission $submission, string $key)
    {
        $isAdmin   = $request->session()->get('admin_authenticated') === true;
        $isOwner   = $request->session()->get('submitted_id') === $submission->id;

        if (!$isAdmin && !$isOwner) {
            abort(403);
        }

        $path = $this->resolveFilePath($submission, $key);
        if (!$path || !Storage::disk(self::DISK)->exists($path)) {
            abort(404);
        }

        $disposition = $request->boolean('download') ? 'attachment' : 'inline';
        $filename    = basename($path);

        return Storage::disk(self::DISK)->response($path, $filename, [], $disposition);
    }

    public function adminLogout(Request $request)
    {
        $request->session()->forget('admin_authenticated');
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.index');
    }

    /**
     * Delete a submission and every uploaded file that belongs to it.
     */
    public function destroy(Request $request, Submission $submission)
    {
        $folder = $this->submissionFolder($submission);

        // Collect a defensive list of paths in case files live outside the
        // computed folder for any reason (e.g. legacy records).
        $paths = array_filter([
            $submission->ci_employe,    $submission->photo_employe,
            $submission->ci_pere,       $submission->photo_pere,
            $submission->ci_mere,       $submission->photo_mere,
            $submission->ci_conjoint,   $submission->photo_conjoint,
        ]);
        foreach (($submission->freres ?? []) as $f) {
            $paths[] = $f['ci']    ?? null;
            $paths[] = $f['photo'] ?? null;
        }
        foreach (($submission->soeurs ?? []) as $s) {
            $paths[] = $s['ci']    ?? null;
            $paths[] = $s['photo'] ?? null;
        }
        foreach (($submission->descendants ?? []) as $d) {
            $paths[] = $d['ci']    ?? null;
            $paths[] = $d['photo'] ?? null;
        }
        $paths = array_filter($paths);

        $disk = Storage::disk(self::DISK);
        foreach ($paths as $p) {
            if ($disk->exists($p)) {
                $disk->delete($p);
            }
        }
        if ($folder && $disk->exists($folder)) {
            $disk->deleteDirectory($folder);
        }

        $id = $submission->id;
        $submission->delete();

        Log::info('Submission deleted', [
            'id'      => $id,
            'folder'  => $folder,
            'by_ip'   => $request->ip(),
        ]);

        return redirect()
            ->route('admin.index')
            ->with('flash_success', "Soumission #{$id} supprimée définitivement.");
    }

    /**
     * Best-effort recovery of the upload folder path from any one stored file.
     */
    private function submissionFolder(Submission $submission): ?string
    {
        $anyPath = $submission->ci_employe
                ?? $submission->photo_employe
                ?? $submission->ci_pere
                ?? $submission->photo_pere
                ?? $submission->ci_mere
                ?? $submission->photo_mere
                ?? $submission->ci_conjoint
                ?? $submission->photo_conjoint;
        return $anyPath ? dirname($anyPath) : null;
    }

    /**
     * Map a "key" (e.g. "ci_employe", "fratrie.0.ci", "descendants.2.photo")
     * back to the stored file path on the submission.
     */
    private function resolveFilePath(Submission $submission, string $key): ?string
    {
        $simple = [
            'ci_employe', 'photo_employe',
            'ci_pere',    'photo_pere',
            'ci_mere',    'photo_mere',
            'ci_conjoint','photo_conjoint',
        ];
        if (in_array($key, $simple, true)) {
            return $submission->{$key} ?? null;
        }

        if (preg_match('/^(freres|soeurs|descendants)\.(\d+)\.(ci|photo)$/', $key, $m)) {
            $list = $submission->{$m[1]} ?? [];
            return $list[(int) $m[2]][$m[3]] ?? null;
        }

        return null;
    }

    /** Reject paths that do not belong to this submission's folder. */
    private function isOwnedPath(?string $oldPath, string $folder): bool
    {
        if (!$oldPath) return false;
        $oldPath = ltrim((string) $oldPath, '/');
        return str_starts_with($oldPath, $folder . '/');
    }

    private function streamSubmissionXlsx(Submission $submission)
    {
        $writer   = new Xlsx($this->buildSpreadsheet(collect([$submission])));
        $filename = 'fiche_' . Str::slug($submission->nom_complet) . '_' . $submission->created_at->format('Ymd') . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function fileLink(Submission $submission, string $key): ?string
    {
        if (!$this->resolveFilePath($submission, $key)) return null;
        return URL::route('files.show', ['submission' => $submission->id, 'key' => $key]);
    }

    private function buildSpreadsheet(\Illuminate\Support\Collection $submissions): Spreadsheet
    {
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

        $coord = fn(int $c, int $r): string => Coordinate::stringFromColumnIndex($c) . $r;

        $row = 2;

        $writeRow = function (
            Submission $s, string $type, ?string $nom, ?string $situation,
            ?string $ciKey, ?string $photoKey, ?string $date, string $bgColor
        ) use ($sheet, &$row, $coord) {
            $sheet->fromArray([$type, $nom ?? '', $situation ?? '', '', '', $date ?? ''], null, "A{$row}");
            $sheet->getStyle("A{$row}:F{$row}")->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
            ]);
            $sheet->getStyle("A{$row}")->getFont()->setBold(true);

            if ($ciKey && $url = $this->fileLink($s, $ciKey)) {
                $c = $coord(4, $row);
                $sheet->setCellValue($c, 'Voir CI');
                $sheet->getCell($c)->setHyperlink(new Hyperlink($url, 'Voir CI'));
                $sheet->getStyle($c)->applyFromArray(['font' => ['color' => ['rgb' => '1155CC'], 'underline' => Font::UNDERLINE_SINGLE]]);
            }
            if ($photoKey && $url = $this->fileLink($s, $photoKey)) {
                $c = $coord(5, $row);
                $sheet->setCellValue($c, 'Voir Photo');
                $sheet->getCell($c)->setHyperlink(new Hyperlink($url, 'Voir Photo'));
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
            $writeRow($s, 'Employé',    $s->nom_complet,  $s->situation_familiale, 'ci_employe',  'photo_employe',  $date, $colors['employe']);
            if ($s->nom_pere   || $s->ci_pere   || $s->photo_pere)   $writeRow($s, 'Père',       $s->nom_pere,    null, 'ci_pere',    'photo_pere',    null, $colors['pere']);
            if ($s->nom_mere   || $s->ci_mere   || $s->photo_mere)   $writeRow($s, 'Mère',       $s->nom_mere,    null, 'ci_mere',    'photo_mere',    null, $colors['mere']);
            if ($s->nom_conjoint||$s->ci_conjoint||$s->photo_conjoint)$writeRow($s, 'Conjoint(e)',$s->nom_conjoint,null,'ci_conjoint','photo_conjoint',null, $colors['conjoint']);
            foreach ($s->descendants ?? [] as $i => $d) $writeRow($s, 'Descendant '.($i+1), $d['nom']??null, null, "descendants.$i.ci", "descendants.$i.photo", null, $colors['descendant']);
            foreach ($s->freres    ?? [] as $i => $f) $writeRow($s, 'Frère '.($i+1),       $f['nom']??null, null, "freres.$i.ci",    "freres.$i.photo",    null, $colors['sibling']);
            foreach ($s->soeurs    ?? [] as $i => $sr)$writeRow($s, 'Sœur '.($i+1),        $sr['nom']??null,null, "soeurs.$i.ci",    "soeurs.$i.photo",    null, $colors['sibling']);
            $row++;
        }

        foreach (range(1, 6) as $colIndex) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($colIndex))->setAutoSize(true);
        }

        return $spreadsheet;
    }
}
