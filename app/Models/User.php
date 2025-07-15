<?php

namespace App\Models;

 use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Section;
use App\Models\Student;
use App\Models\Submission;
use App\Models\StudentQuizResult;
use App\Models\TeacherAssignment;
// use Illuminate\Auth\MustVerifyEmail;
use App\Models\StudentProficiencyResult;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',

    ];
    public function teacherAssignments()
    {
        return $this->hasMany(TeacherAssignment::class, 'user_id');
    }

    public function submissions() {
        return $this->hasMany(Submission::class);
    }
    public function enrollments()
    {
        return $this->hasMany(Student::class, 'user_id');
    }

    public function proficiencyResults()
    {
        return $this->hasMany(StudentProficiencyResult::class);
    }
    public function section()
    {
        return $this->belongsTo(Section::class);
    }
    public function quizResults()
{
    return $this->hasMany(StudentQuizResult::class, 'user_id');
}
            /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
