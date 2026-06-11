<?php

use Illuminate\Support\Facades\Schedule;

// ─── NasiyaPro Scheduler ─────────────────────────────────────────
// Har kuni yarim tunda muddati o'tgan to'lovlarni yangilash
Schedule::command('nasiya:muddat-kuzatish')->dailyAt('00:05');
