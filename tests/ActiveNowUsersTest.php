<?php

namespace Rainwater\Active\Tests;

use Rainwater\Active\Active;

class ActiveUsersTest extends TestCase
{
    /** @test */
    public function show_the_active_user_list_within_5_minutes_by_default()
    {
        $sessions = collect([
            $this->createSessionWithUser(1),
            $this->createSessionWithUser(4 * 60)
        ])->pluck('last_activity');

        $this->createSessionWithUser(6 * 60);
        $this->createSession();

        $users = Active::users()->get()->pluck('last_activity');

        $this->assertEquals($sessions, $users);
    }

    /** @test */
    public function can_get_all_active_users_within_last_2_minutes()
    {
        $this->createSessionWithUser();
        $this->createSessionWithUser(60);
        $this->createSessionWithUser(2 * 60);
        $this->createSessionWithUser(2 * 60 + 1);

        $users = Active::users(2);
        $usersWithinMinutes = Active::usersWithinMinutes(2);

        $this->assertEquals(3, $users->count());
        $this->assertEquals(3, $usersWithinMinutes->count());
    }

    /** @test */
    public function show_all_active_users_within_the_past_4_seconds()
    {
        $this->createSessionWithUser();
        $this->createSessionWithUser(3);
        $this->createSessionWithUser(4);
        $this->createSessionWithUser(5);

        $users = Active::usersWithinSeconds(4)->get()->pluck('last_activity');

        $this->assertEquals(3, $users->count());
    }

    /** @test */
    public function show_all_active_users_within_the_past_3_hours()
    {
        $this->createSessionWithUser();
        $this->createSessionWithUser(10 * 60); // == 10 minutes
        $this->createSessionWithUser(25 * 60); // == 25 minutes
        $this->createSessionWithUser(2 * 60 * 60); // == 2 hours
        $this->createSessionWithUser(59 * 3 * 60); // == 2 hours 57 minutes
        $this->createSessionWithUser(4 * 60 * 60); // === 4 hours

        $users = Active::usersWithinHours(3)->get()->pluck('last_activity');

        $this->assertEquals(5, $users->count());
    }

    /** @test */
    public function can_sort_user_list_by_most_recent()
    {
        $sessions = collect([
            $this->createSessionWithUser(30),
            $this->createSessionWithUser(1),
            $this->createSessionWithUser(25),
            $this->createSessionWithUser(60),
            $this->createSessionWithUser(6),
        ]);

        $users = Active::users()->mostRecent()->get();

        $this->assertEquals($sessions->sortByDesc('last_activity')->pluck('id'), $users->pluck('id'));
    }

    /** @test */
    public function can_sort_user_list_by_user_column()
    {
        $sessions = collect([
            $this->createSessionWithUser(30),
            $this->createSessionWithUser(1),
            $this->createSessionWithUser(25),
            $this->createSessionWithUser(60),
            $this->createSessionWithUser(6),
        ]);

        $users = Active::users()->orderByUser('name')->get();
        $usersDescending = Active::users()->orderByUser('name', 'desc')->get();

        $this->assertEquals($sessions->sortBy('user.name')->pluck('id'), $users->pluck('id'));
        $this->assertEquals($sessions->sortByDesc('user.name')->pluck('id'), $usersDescending->pluck('id'));
    }
}
