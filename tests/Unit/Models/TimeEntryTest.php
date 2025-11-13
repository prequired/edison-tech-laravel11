<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Invoice;
use App\Models\Project;
use App\Models\Task;
use App\Models\TimeEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * TimeEntry Model Unit Tests
 *
 * Tests the TimeEntry model including:
 * - Factory creation
 * - Relationships (project, task, user, invoice)
 * - Time calculations
 * - Billable hours tracking
 * - Rate and amount calculations
 * - Invoice associations
 * - Type casting
 */
class TimeEntryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test time entry can be created using factory
     */
    public function test_time_entry_can_be_created_using_factory(): void
    {
        $timeEntry = TimeEntry::factory()->create();

        $this->assertInstanceOf(TimeEntry::class, $timeEntry);
        $this->assertDatabaseHas('time_entries', [
            'id' => $timeEntry->id,
        ]);
    }

    /**
     * Test time entry belongs to project
     */
    public function test_time_entry_belongs_to_project(): void
    {
        $project = Project::factory()->create();
        $timeEntry = TimeEntry::factory()->create(['project_id' => $project->id]);

        $this->assertInstanceOf(Project::class, $timeEntry->project);
        $this->assertEquals($project->id, $timeEntry->project->id);
    }

    /**
     * Test time entry belongs to task
     */
    public function test_time_entry_belongs_to_task(): void
    {
        $task = Task::factory()->create();
        $timeEntry = TimeEntry::factory()->create([
            'project_id' => $task->project_id,
            'task_id' => $task->id,
        ]);

        $this->assertInstanceOf(Task::class, $timeEntry->task);
        $this->assertEquals($task->id, $timeEntry->task->id);
    }

    /**
     * Test time entry can be without task
     */
    public function test_time_entry_can_be_without_task(): void
    {
        $timeEntry = TimeEntry::factory()->create(['task_id' => null]);

        $this->assertNull($timeEntry->task_id);
        $this->assertNull($timeEntry->task);
    }

    /**
     * Test time entry belongs to user
     */
    public function test_time_entry_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $timeEntry = TimeEntry::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $timeEntry->user);
        $this->assertEquals($user->id, $timeEntry->user->id);
    }

    /**
     * Test time entry can belong to invoice
     */
    public function test_time_entry_can_belong_to_invoice(): void
    {
        $invoice = Invoice::factory()->create();
        $timeEntry = TimeEntry::factory()->create([
            'invoice_id' => $invoice->id,
            'is_invoiced' => true,
        ]);

        $this->assertInstanceOf(Invoice::class, $timeEntry->invoice);
        $this->assertEquals($invoice->id, $timeEntry->invoice->id);
    }

    /**
     * Test time entry without invoice
     */
    public function test_time_entry_without_invoice(): void
    {
        $timeEntry = TimeEntry::factory()->create([
            'invoice_id' => null,
            'is_invoiced' => false,
        ]);

        $this->assertNull($timeEntry->invoice_id);
        $this->assertFalse($timeEntry->is_invoiced);
    }

    /**
     * Test start time is cast to datetime
     */
    public function test_start_time_is_cast_to_datetime(): void
    {
        $timeEntry = TimeEntry::factory()->create([
            'start_time' => '2025-01-15 09:00:00',
        ]);

        $this->assertInstanceOf(\Carbon\Carbon::class, $timeEntry->start_time);
        $this->assertEquals('2025-01-15 09:00:00', $timeEntry->start_time->format('Y-m-d H:i:s'));
    }

    /**
     * Test end time is cast to datetime
     */
    public function test_end_time_is_cast_to_datetime(): void
    {
        $timeEntry = TimeEntry::factory()->create([
            'start_time' => '2025-01-15 09:00:00',
            'end_time' => '2025-01-15 17:00:00',
        ]);

        $this->assertInstanceOf(\Carbon\Carbon::class, $timeEntry->end_time);
        $this->assertEquals('2025-01-15 17:00:00', $timeEntry->end_time->format('Y-m-d H:i:s'));
    }

    /**
     * Test end time can be null
     */
    public function test_end_time_can_be_null(): void
    {
        $timeEntry = TimeEntry::factory()->create([
            'end_time' => null,
        ]);

        $this->assertNull($timeEntry->end_time);
    }

    /**
     * Test hours is cast to decimal
     */
    public function test_hours_is_cast_to_decimal(): void
    {
        $timeEntry = TimeEntry::factory()->create(['hours' => 8.5]);

        $this->assertEquals('8.50', $timeEntry->hours);
        $this->assertIsString($timeEntry->hours);
    }

    /**
     * Test hours precision
     */
    public function test_hours_precision(): void
    {
        $timeEntry = TimeEntry::factory()->create(['hours' => 7.567]);

        // Should be rounded to 2 decimal places
        $this->assertEquals('7.57', $timeEntry->hours);
    }

    /**
     * Test is billable flag is cast to boolean
     */
    public function test_is_billable_flag_is_cast_to_boolean(): void
    {
        $billable = TimeEntry::factory()->create(['is_billable' => true]);
        $nonBillable = TimeEntry::factory()->create(['is_billable' => false]);

        $this->assertIsBool($billable->is_billable);
        $this->assertTrue($billable->is_billable);
        $this->assertFalse($nonBillable->is_billable);
    }

    /**
     * Test hourly rate is cast to decimal
     */
    public function test_hourly_rate_is_cast_to_decimal(): void
    {
        $timeEntry = TimeEntry::factory()->create(['hourly_rate' => 150.00]);

        $this->assertEquals('150.00', $timeEntry->hourly_rate);
        $this->assertIsString($timeEntry->hourly_rate);
    }

    /**
     * Test hourly rate can be null
     */
    public function test_hourly_rate_can_be_null(): void
    {
        $timeEntry = TimeEntry::factory()->create(['hourly_rate' => null]);

        $this->assertNull($timeEntry->hourly_rate);
    }

    /**
     * Test amount is cast to decimal
     */
    public function test_amount_is_cast_to_decimal(): void
    {
        $timeEntry = TimeEntry::factory()->create([
            'hours' => 8.0,
            'hourly_rate' => 100.00,
            'amount' => 800.00,
        ]);

        $this->assertEquals('800.00', $timeEntry->amount);
        $this->assertIsString($timeEntry->amount);
    }

    /**
     * Test amount calculation
     */
    public function test_amount_calculation(): void
    {
        $timeEntry = TimeEntry::factory()->create([
            'hours' => 7.5,
            'hourly_rate' => 120.00,
            'amount' => 900.00,
        ]);

        $expectedAmount = 7.5 * 120.00;
        $this->assertEquals('900.00', $timeEntry->amount);
        $this->assertEquals($expectedAmount, (float) $timeEntry->amount);
    }

    /**
     * Test is invoiced flag is cast to boolean
     */
    public function test_is_invoiced_flag_is_cast_to_boolean(): void
    {
        $invoiced = TimeEntry::factory()->create(['is_invoiced' => true]);
        $notInvoiced = TimeEntry::factory()->create(['is_invoiced' => false]);

        $this->assertIsBool($invoiced->is_invoiced);
        $this->assertTrue($invoiced->is_invoiced);
        $this->assertFalse($notInvoiced->is_invoiced);
    }

    /**
     * Test time entry has timestamps
     */
    public function test_time_entry_has_timestamps(): void
    {
        $timeEntry = TimeEntry::factory()->create();

        $this->assertNotNull($timeEntry->created_at);
        $this->assertNotNull($timeEntry->updated_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $timeEntry->created_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $timeEntry->updated_at);
    }

    /**
     * Test time entry mass assignment
     */
    public function test_time_entry_mass_assignment(): void
    {
        $project = Project::factory()->create();
        $task = Task::factory()->create(['project_id' => $project->id]);
        $user = User::factory()->create();

        $timeEntry = TimeEntry::create([
            'project_id' => $project->id,
            'task_id' => $task->id,
            'user_id' => $user->id,
            'description' => 'Working on feature',
            'start_time' => now(),
            'end_time' => now()->addHours(8),
            'hours' => 8.0,
            'is_billable' => true,
            'hourly_rate' => 100.00,
            'amount' => 800.00,
            'is_invoiced' => false,
        ]);

        $this->assertEquals('Working on feature', $timeEntry->description);
        $this->assertEquals('8.00', $timeEntry->hours);
        $this->assertTrue($timeEntry->is_billable);
    }

    /**
     * Test billable time entry
     */
    public function test_billable_time_entry(): void
    {
        $timeEntry = TimeEntry::factory()->create([
            'is_billable' => true,
            'hourly_rate' => 150.00,
            'hours' => 5.0,
            'amount' => 750.00,
        ]);

        $this->assertTrue($timeEntry->is_billable);
        $this->assertNotNull($timeEntry->hourly_rate);
        $this->assertNotNull($timeEntry->amount);
    }

    /**
     * Test non-billable time entry
     */
    public function test_non_billable_time_entry(): void
    {
        $timeEntry = TimeEntry::factory()->create([
            'is_billable' => false,
            'hourly_rate' => null,
            'amount' => null,
        ]);

        $this->assertFalse($timeEntry->is_billable);
        $this->assertNull($timeEntry->hourly_rate);
        $this->assertNull($timeEntry->amount);
    }

    /**
     * Test multiple time entries for same project
     */
    public function test_multiple_time_entries_for_same_project(): void
    {
        $project = Project::factory()->create();
        $user = User::factory()->create();

        TimeEntry::factory()->count(5)->create([
            'project_id' => $project->id,
            'user_id' => $user->id,
        ]);

        $this->assertCount(5, $project->timeEntries);
    }

    /**
     * Test multiple time entries for same task
     */
    public function test_multiple_time_entries_for_same_task(): void
    {
        $task = Task::factory()->create();
        $user = User::factory()->create();

        TimeEntry::factory()->count(3)->create([
            'project_id' => $task->project_id,
            'task_id' => $task->id,
            'user_id' => $user->id,
        ]);

        $this->assertCount(3, $task->timeEntries);
    }

    /**
     * Test time entry duration calculation
     */
    public function test_time_entry_duration_calculation(): void
    {
        $startTime = now()->setTime(9, 0, 0);
        $endTime = now()->setTime(17, 30, 0);

        $timeEntry = TimeEntry::factory()->create([
            'start_time' => $startTime,
            'end_time' => $endTime,
            'hours' => 8.5,
        ]);

        $diffInHours = $startTime->diffInMinutes($endTime) / 60;
        $this->assertEquals(8.5, $diffInHours);
        $this->assertEquals('8.50', $timeEntry->hours);
    }

    /**
     * Test invoiced time entry cannot be modified
     */
    public function test_invoiced_time_entry_properties(): void
    {
        $invoice = Invoice::factory()->create();
        $timeEntry = TimeEntry::factory()->create([
            'invoice_id' => $invoice->id,
            'is_invoiced' => true,
            'is_billable' => true,
        ]);

        $this->assertTrue($timeEntry->is_invoiced);
        $this->assertNotNull($timeEntry->invoice_id);
        $this->assertEquals($invoice->id, $timeEntry->invoice_id);
    }

    /**
     * Test time entry with description
     */
    public function test_time_entry_with_description(): void
    {
        $timeEntry = TimeEntry::factory()->create([
            'description' => 'Implemented user authentication feature',
        ]);

        $this->assertEquals('Implemented user authentication feature', $timeEntry->description);
    }

    /**
     * Test time entry description can be null
     */
    public function test_time_entry_description_can_be_null(): void
    {
        $timeEntry = TimeEntry::factory()->create(['description' => null]);

        $this->assertNull($timeEntry->description);
    }

    /**
     * Test time entry filtering by billable status
     */
    public function test_time_entry_filtering_by_billable_status(): void
    {
        $project = Project::factory()->create();

        TimeEntry::factory()->count(3)->create([
            'project_id' => $project->id,
            'is_billable' => true,
        ]);

        TimeEntry::factory()->count(2)->create([
            'project_id' => $project->id,
            'is_billable' => false,
        ]);

        $billableEntries = TimeEntry::where('is_billable', true)->get();
        $nonBillableEntries = TimeEntry::where('is_billable', false)->get();

        $this->assertCount(3, $billableEntries);
        $this->assertCount(2, $nonBillableEntries);
    }

    /**
     * Test time entry filtering by invoiced status
     */
    public function test_time_entry_filtering_by_invoiced_status(): void
    {
        $project = Project::factory()->create();

        TimeEntry::factory()->count(4)->create([
            'project_id' => $project->id,
            'is_invoiced' => true,
        ]);

        TimeEntry::factory()->count(6)->create([
            'project_id' => $project->id,
            'is_invoiced' => false,
        ]);

        $invoicedEntries = TimeEntry::where('is_invoiced', true)->get();
        $uninvoicedEntries = TimeEntry::where('is_invoiced', false)->get();

        $this->assertCount(4, $invoicedEntries);
        $this->assertCount(6, $uninvoicedEntries);
    }

    /**
     * Test total hours calculation for project
     */
    public function test_total_hours_calculation_for_project(): void
    {
        $project = Project::factory()->create();

        TimeEntry::factory()->create(['project_id' => $project->id, 'hours' => 8.0]);
        TimeEntry::factory()->create(['project_id' => $project->id, 'hours' => 6.5]);
        TimeEntry::factory()->create(['project_id' => $project->id, 'hours' => 4.25]);

        $totalHours = TimeEntry::where('project_id', $project->id)->sum('hours');

        $this->assertEquals(18.75, $totalHours);
    }

    /**
     * Test total amount calculation for project
     */
    public function test_total_amount_calculation_for_project(): void
    {
        $project = Project::factory()->create();

        TimeEntry::factory()->create([
            'project_id' => $project->id,
            'hours' => 8.0,
            'hourly_rate' => 100.00,
            'amount' => 800.00,
            'is_billable' => true,
        ]);

        TimeEntry::factory()->create([
            'project_id' => $project->id,
            'hours' => 5.0,
            'hourly_rate' => 120.00,
            'amount' => 600.00,
            'is_billable' => true,
        ]);

        $totalAmount = TimeEntry::where('project_id', $project->id)
            ->where('is_billable', true)
            ->sum('amount');

        $this->assertEquals(1400.00, $totalAmount);
    }

    /**
     * Test uninvoiced billable hours for project
     */
    public function test_uninvoiced_billable_hours_for_project(): void
    {
        $project = Project::factory()->create();

        TimeEntry::factory()->count(3)->create([
            'project_id' => $project->id,
            'is_billable' => true,
            'is_invoiced' => false,
            'hours' => 8.0,
        ]);

        TimeEntry::factory()->count(2)->create([
            'project_id' => $project->id,
            'is_billable' => true,
            'is_invoiced' => true,
            'hours' => 8.0,
        ]);

        $uninvoicedHours = TimeEntry::where('project_id', $project->id)
            ->where('is_billable', true)
            ->where('is_invoiced', false)
            ->sum('hours');

        $this->assertEquals(24.0, $uninvoicedHours);
    }

    /**
     * Test time entries by date range
     */
    public function test_time_entries_by_date_range(): void
    {
        $project = Project::factory()->create();

        TimeEntry::factory()->create([
            'project_id' => $project->id,
            'start_time' => '2025-01-10 09:00:00',
        ]);

        TimeEntry::factory()->create([
            'project_id' => $project->id,
            'start_time' => '2025-01-15 09:00:00',
        ]);

        TimeEntry::factory()->create([
            'project_id' => $project->id,
            'start_time' => '2025-01-25 09:00:00',
        ]);

        $entries = TimeEntry::whereDate('start_time', '>=', '2025-01-15')
            ->whereDate('start_time', '<=', '2025-01-31')
            ->get();

        $this->assertCount(2, $entries);
    }
}
