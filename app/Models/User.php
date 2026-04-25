<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\RoleName;
use App\Models\Department;
use App\Models\Evaluation;
use App\Models\Feedback;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\JuryAssignment;
use App\Models\Milestone;
use App\Models\Notification;
use App\Models\PlagiarismReport;
use App\Models\ProjectTopic;
use App\Models\Role;
use App\Models\Rubric;
use App\Models\Submission;
use App\Models\SupervisorAssignment;
use App\Models\SupervisorProfile;
use App\Models\UserDepartment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'is_active',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'is_active' => 'bool',
        'password' => 'hashed',
    ];

    // Role assigned to this user.
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    // Supervisor profile linked to this user.
    public function supervisorProfile(): HasOne
    {
        return $this->hasOne(SupervisorProfile::class, 'user_id');
    }

    // Department link rows for this user.
    public function userDepartments(): HasMany
    {
        return $this->hasMany(UserDepartment::class, 'user_id');
    }

    // Departments associated with this user.
    public function departments(): BelongsToMany
    {
        return $this->belongsToMany(Department::class, 'users_departments', 'user_id', 'department_id');
    }

    // Groups created by this user.
    public function createdGroups(): HasMany
    {
        return $this->hasMany(Group::class, 'created_by');
    }

    // Membership rows that link this user to groups.
    public function groupMemberships(): HasMany
    {
        return $this->hasMany(GroupMember::class, 'user_id');
    }

    // Groups where this user is a member.
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_members', 'user_id', 'group_id');
    }

    // Project topics proposed by this user.
    public function projectTopics(): HasMany
    {
        return $this->hasMany(ProjectTopic::class, 'created_by');
    }

    // Supervisor assignments belonging to this user.
    public function supervisorAssignments(): HasMany
    {
        return $this->hasMany(SupervisorAssignment::class, 'supervisor_id');
    }

    // Jury assignments belonging to this user as evaluator.
    public function juryAssignments(): HasMany
    {
        return $this->hasMany(JuryAssignment::class, 'evaluator_id');
    }

    // Milestones created by this user.
    public function createdMilestones(): HasMany
    {
        return $this->hasMany(Milestone::class, 'created_by');
    }

    // Submissions uploaded by this user.
    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class, 'submitted_by');
    }

    // Feedback entries authored by this user.
    public function feedbackEntries(): HasMany
    {
        return $this->hasMany(Feedback::class, 'user_id');
    }

    // Plagiarism reports created by this user.
    public function plagiarismReportsCreated(): HasMany
    {
        return $this->hasMany(PlagiarismReport::class, 'created_by');
    }

    // Rubrics authored by this user.
    public function rubricsCreated(): HasMany
    {
        return $this->hasMany(Rubric::class, 'created_by');
    }

    // Evaluations submitted by this user.
    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class, 'evaluator_id');
    }

    // Notifications delivered to this user.
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    public function isSupervisor(): bool
    {
        return $this->role?->name === RoleName::Supervisor;
    }

    public function isStudent(): bool
    {
        return $this->role?->name === RoleName::Student;
    }

    public function isEvaluator(): bool
    {
        return $this->role?->name === RoleName::Evaluator;
    }

    public function isCoordinator(): bool
    {
        return $this->role?->name === RoleName::Coordinator;
    }

    public function isAdmin(): bool
    {
        return $this->role?->name === RoleName::Admin;
    }
}
