<?php

namespace App\Http\Controllers;

use App\Models\University;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DepartmentController extends BaseController
{
    public function index(Request $request): JsonResponse
    {
        $universityCode = strtoupper(trim((string) $request->query('university_code', '')));

        if ($universityCode !== 'LASU') {
            return $this->error('Structured departments not available for this university', 404);
        }

        $facultiesConfig = config('lasu_departments.faculties', []);
        $schoolsConfig = config('lasu_departments.schools_and_directorates', []);

        $faculties = [];
        $schools = [];
        $facultyLabels = [];
        $schoolLabels = [];
        $allDepartments = [];

        foreach ($facultiesConfig as $key => $faculty) {
            $departmentsList = array_values($faculty['departments'] ?? []);
            $faculties[$key] = $departmentsList;
            $facultyLabels[$key] = $faculty['name'] ?? ('Faculty of '.ucwords(str_replace('_', ' ', $key)));
            foreach ($faculty['departments'] ?? [] as $deptName) {
                $allDepartments[] = $deptName;
            }
        }

        foreach ($schoolsConfig as $key => $school) {
            $departmentsList = array_values($school['departments'] ?? []);
            $schools[$key] = $departmentsList;
            $schoolLabels[$key] = $school['name'] ?? ucwords(str_replace('_', ' ', $key));
            foreach ($school['departments'] ?? [] as $deptName) {
                $allDepartments[] = $deptName;
            }
        }

        return $this->success([
            'university_code' => $universityCode,
            'faculties' => $faculties,
            'faculties_labels' => $facultyLabels,
            'schools' => $schools,
            'schools_labels' => $schoolLabels,
            'all_departments' => array_values(array_unique($allDepartments)),
        ], 'Departments retrieved successfully');
    }

    public function byUniversity(string $universityCode): JsonResponse
    {
        $universityCode = strtoupper(trim($universityCode));

        $university = University::where('code', $universityCode)->first();

        if (! $university) {
            return $this->error('University not found', 404);
        }

        if (! $university->has_structured_departments) {
            return $this->error('Structured departments not available for this university', 404);
        }

        $dbDepartments = $university->departments()
            ->where('is_active', true)
            ->orderBy('faculty')
            ->orderBy('name')
            ->get(['id', 'name', 'code', 'faculty', 'unit_type']);

        if ($dbDepartments->isNotEmpty()) {
            return $this->success([
                'university_code' => $universityCode,
                'name' => $university->name,
                'departments' => $dbDepartments,
            ], 'Departments retrieved successfully');
        }

        if ($universityCode === 'LASU') {
            $config = array_merge(
                config('lasu_departments.faculties', []),
                config('lasu_departments.schools_and_directorates', [])
            );

            $units = [];
            $allDepartments = [];

            foreach ($config as $key => $unit) {
                $units[$key] = [
                    'name' => $unit['name'] ?? $key,
                    'code' => $unit['code'] ?? $key,
                    'unit_type' => $unit['type'] ?? 'faculty',
                    'departments' => $unit['departments'] ?? [],
                ];
                foreach ($unit['departments'] ?? [] as $deptKey => $deptName) {
                    $allDepartments[$deptKey] = $deptName;
                }
            }

            return $this->success([
                'university_code' => $universityCode,
                'name' => $university->name,
                'units' => $units,
                'all_departments' => $allDepartments,
            ], 'Departments retrieved successfully');
        }

        return $this->success([
            'university_code' => $universityCode,
            'name' => $university->name,
            'units' => [],
            'all_departments' => [],
        ], 'Departments retrieved successfully');
    }
}
