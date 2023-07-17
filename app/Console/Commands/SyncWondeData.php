<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\School;
use App\Jobs\ProcessEmployeeData;

class SyncWondeData extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'app:sync-wonde-data';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Syncs the database with the Wonde API';

  /**
   * Execute the console command.
   */
  public function handle()
  {
    $wondeSchoolID = 'A1930499544';
    $wondeToken = config('app.wonde_token');
    $client = new \Wonde\Client($wondeToken);
    $wondeSchoolData = $client->schools->get($wondeSchoolID);

    // Update or Create the School data
    $school = School::updateOrCreate(
      [
        'wonde_id' => $wondeSchoolData->id
      ],
      [
        'name' => $wondeSchoolData->name,
        'address_line_1' => $wondeSchoolData->address->address_line_1,
        'address_line_2' => $wondeSchoolData->address->address_line_2,
        'town' => $wondeSchoolData->address->address_town,
        'postcode' => $wondeSchoolData->address->address_postcode
      ]
    );

    // Load the School so that we can access the relationships
    $wondeSchool = $client->school($wondeSchoolID);

    // Unable to access the pagination meta information as the meta property is private
    // If there are 50 Employees returned, there's a chance that there are more Employees
    // Make another call to the next page of Employees API to see if any results are returned
    $employeeData = [];
    $page = 1;
    $hasMoreEmployees = true;

    // We don't want to sync all of the data all of the time
    // After the initial sync, we only need to sync data that has been updated/added since the last sync
    // If the School was not recently created, we know the initial sync has already happened
    $params = ['page' => 1, 'has_class' => true, 'has_contract' => true, 'has_class' => true];
    if (!$school->wasRecentlyCreated) {
      $lastUpdatedAt = $school->updated_at->format('Y-m-d H:i:s');
      $params['updated_after'] = $lastUpdatedAt;
    }

    while ($hasMoreEmployees) {
      $moreEmployees = $wondeSchool->employees->all(['classes'], $params);

      if (count($moreEmployees->array) === 50) {
        $hasMoreEmployees = true;
      } else {
        $hasMoreEmployees = false;
      }

      foreach ($moreEmployees->array as $employee) {
        array_push($employeeData, $employee);
      }

      $params['page']++;
    }

    // Loop over each Employee and dispatch a job to process the data
    foreach ($employeeData as $employee) {
      ProcessEmployeeData::dispatch($employee, $school, $wondeSchool);
    }

    // Ensure the 'updated_at' column is updated to identify when the last sync was
    $school->touch();

    return 0;
  }
}
