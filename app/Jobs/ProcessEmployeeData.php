<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\School;

class ProcessEmployeeData implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  public $employee;
  public $school;
  public $wondeSchool;

  /**
   * Create a new job instance.
   */
  public function __construct($employee, School $school, $wondeSchool)
  {
    $this->employee = $employee;
    $this->school = $school;
    $this->wondeSchool = $wondeSchool;
  }

  /**
   * Execute the job.
   */
  public function handle(): void
  {
      $employee = $this->employee;
      $school = $this->school;
      $wondeSchool = $this->wondeSchool;

      // Update or Create the Employee data
    $savedEmployee = $school->employees()->updateOrCreate(
      [
        'wonde_id' => $employee->id
      ],
      [
        'title' => $employee->title,
        'initials' => $employee->initials,
        'surname' => $employee->surname,
        'forename' => $employee->forename,
      ]
    );

    // Get and Save each of the Employee's Classes
    foreach ($employee->classes->data as $class) {
      $savedClass = $savedEmployee->schoolClasses()->updateOrCreate(
        [
          'school_id' => $school->id,
          'wonde_id' => $class->id
        ],
        [
          'name' => $class->name
        ]
      );

      // Get the related Class data
      // i.e. the Students and Lessons
      $wondeClassData = $wondeSchool->classes->get($class->id, ['students', 'lessons.period'], ['has_lessons' => true, 'has_students' => true]);

      // Save the Lessons for each Class
      foreach ($wondeClassData->lessons->data as $lesson) {
        $savedClass->lessons()->updateOrCreate(
          [
            'wonde_id' => $lesson->id
          ],
          [
            'name' => $lesson->period->data->name,
            'starts_at' => $lesson->start_at->date,
            'ends_at' => $lesson->end_at->date,
          ]
        );
      }

      // Save the Students for each Class
      foreach ($wondeClassData->students->data as $student) {
        $savedStudent = $school->students()->updateOrCreate(
          [
            'wonde_id' => $student->id
          ],
          [
            'initials' => $student->initials,
            'surname' => $student->surname,
            'forename' => $student->forename,
          ]
        );

        $savedClass->students()->updateOrCreate(['student_id' => $savedStudent->id]);
      }
    }
  }
}
