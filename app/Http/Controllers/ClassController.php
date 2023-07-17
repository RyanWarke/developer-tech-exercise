<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use Carbon\Carbon;

class ClassController extends Controller
{
    public function getEmployeesClasses(Request $request, Employee $employee)
    {
        $startOfTheWeek = Carbon::now()->startOfWeek()->format('Y-m-d');
        $endOfTheWeek = Carbon::now()->endOfWeek()->format('Y-m-d');

        $classes = $employee->schoolClasses()->with(['students.student', 'lessons'])->whereHas('lessons', function ($query) use ($startOfTheWeek, $endOfTheWeek) {
            return $query->whereBetween('starts_at', [$startOfTheWeek, $endOfTheWeek]);
        })->get();

        // Get the dates for Monday - Friday of this week
        $employeeClasses = [];
        $date = Carbon::now()->startOfWeek();
        for ($i = 0; $i < 5; $i++) {
            $employeeClasses[] = [
                'date' => $date->format('Y-m-d'),
                'classes' => []
            ];
            $date->addDay();
        }

        // Divide the Classes, Lessons and Students into the days they occur
        foreach ($classes as $class) {
            foreach ($class->lessons as $lesson) {
                $index = array_search($lesson->starts_at->format('Y-m-d'), array_column($employeeClasses, 'date'));
                $employeeClasses[$index]['classes'][] = [
                    'name' => $class->name,
                    'students' => $class->students,
                    'lesson' => $lesson
                ];
            }
        }

        return response()->json(['employeeClasses' => $employeeClasses], 200);
    }
}
