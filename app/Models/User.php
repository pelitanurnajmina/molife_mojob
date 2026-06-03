<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'username',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function userData(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(UserData::class);
    }

    /* ── Relational profile + activity relations ── */
    public function profile()       { return $this->hasOne(UserProfile::class); }
    public function features()      { return $this->hasMany(UserFeature::class); }
    public function careerGoal()    { return $this->hasOne(CareerGoal::class); }

    public function sholatPrayers() { return $this->hasMany(SholatPrayer::class); }
    public function sholatSunnah()  { return $this->hasMany(SholatSunnah::class); }
    public function spiritualLogs() { return $this->hasMany(SpiritualLog::class); }
    public function gymLogs()       { return $this->hasMany(GymLog::class); }
    public function runLogs()       { return $this->hasMany(RunLog::class); }
    public function cyclingLogs()   { return $this->hasMany(CyclingLog::class); }
    public function swimmingLogs()  { return $this->hasMany(SwimmingLog::class); }
    public function racketLogs()    { return $this->hasMany(RacketLog::class); }
    public function customSportLogs(){ return $this->hasMany(CustomSportLog::class); }
    public function intimacyLogs()  { return $this->hasMany(IntimacyLog::class); }
    public function moodLogs()      { return $this->hasMany(MoodLog::class); }
    public function reflections()   { return $this->hasMany(Reflection::class); }
    public function notes()         { return $this->hasMany(Note::class); }
    public function todos()         { return $this->hasMany(Todo::class); }
    public function goals()         { return $this->hasMany(Goal::class); }
    public function reminders()     { return $this->hasMany(Reminder::class); }

    public function jobApplications(){ return $this->hasMany(JobApplication::class); }
    public function interviews()    { return $this->hasMany(Interview::class); }
    public function prepLinks()     { return $this->hasMany(PrepLink::class); }
    public function prepFiles()     { return $this->hasMany(PrepFile::class); }
    public function prepTemplates() { return $this->hasMany(PrepTemplate::class); }
    public function prepQa()        { return $this->hasMany(PrepQa::class); }
    public function contacts()      { return $this->hasMany(Contact::class); }

    public function financeTransactions() { return $this->hasMany(FinanceTransaction::class); }
    public function financeBudgets()      { return $this->hasMany(FinanceBudget::class); }
    public function financeSavingsGoals() { return $this->hasMany(FinanceSavingsGoal::class); }
    public function referralPayouts()     { return $this->hasMany(ReferralPayout::class); }
}
