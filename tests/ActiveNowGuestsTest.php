<?php

namespace Rainwater\Active\Tests;

use Rainwater\Active\Active;

class ActiveGuestsTest extends TestCase
{
    /** @test */
    public function show_the_active_guest_list_within_5_minutes_by_default()
    {
        $sessions = collect([
            $this->createSession(1),
            $this->createSession(4 * 60)
        ])->pluck('last_activity');

        $this->createSession(6 * 60);
        $this->createSessionWithUser();

        $guests = Active::guests()->get()->pluck('last_activity');

        $this->assertEquals($sessions, $guests);
    }

    /** @test */
    public function can_get_all_active_users_within_last_2_minutes()
    {
        $this->createSession();
        $this->createSession(60);
        $this->createSession(2 * 60);
        $this->createSession(2 * 60 + 1);

        $guests = Active::guests(2);
        $guestsWithinMinutes = Active::guestsWithinMinutes(2);

        $this->assertEquals(3, $guests->count());
        $this->assertEquals(3, $guestsWithinMinutes->count());
    }

    /** @test */
    public function show_all_active_users_within_the_past_4_seconds()
    {
        $this->createSession();
        $this->createSession(3);
        $this->createSession(4);
        $this->createSession(5);

        $guests = Active::guestsWithinSeconds(4)->get()->pluck('last_activity');

        $this->assertEquals(3, $guests->count());
    }

    /** @test */
    public function show_all_active_users_within_the_past_3_hours()
    {
        $this->createSession();
        $this->createSession(10 * 60); // == 10 minutes
        $this->createSession(25 * 60); // == 25 minutes
        $this->createSession(2 * 60 * 60); // == 2 hours
        $this->createSession(59 * 3 * 60); // == 2 hours 57 minutes
        $this->createSession(4 * 60 * 60); // === 4 hours

        $guests = Active::guestsWithinHours(3)->get()->pluck('last_activity');

        $this->assertEquals(5, $guests->count());
    }

    /** @test */
    public function can_sort_user_list_by_most_recent()
    {
        $sessions = collect([
            $this->createSession(30),
            $this->createSession(1),
            $this->createSession(25),
            $this->createSession(60),
            $this->createSession(6),
        ]);

        $guests = Active::guests()->mostRecent()->get();

        $this->assertEquals($sessions->sortByDesc('last_activity')->pluck('id'), $guests->pluck('id'));
    }
}
