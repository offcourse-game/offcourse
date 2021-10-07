<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    { //add default user
        DB::table('users')->insert([
            'name' => 'TestUser',
            'email' => 'test@test.de',
            'password' => bcrypt('2YhwqTB'),
        ]);

        // add a test session
        DB::table('session')->insert([
            'session_name' => 'Test-Session',
            'prof_id' => '1'
        ]);

        DB::table('boss')->insert([
            'boss_id' => 1,
            'boss_life' => 0,
            'boss_life_start' => 0,
            'session_id' => 1
        ]);

        // add 12 questions
        DB::table('questions')->insert([
            ['question_text' => 'This is question 1!', 'session_id' => '1'],
            ['question_text' => 'This is question 2!', 'session_id' => '1'],
            ['question_text' => 'This is question 3!', 'session_id' => '1'],
            ['question_text' => 'This is question 4!', 'session_id' => '1'],
            ['question_text' => 'This is question 5!', 'session_id' => '1'],
            ['question_text' => 'This is question 6!', 'session_id' => '1'],
            ['question_text' => 'This is question 7!', 'session_id' => '1'],
            ['question_text' => 'This is question 8!', 'session_id' => '1'],
            ['question_text' => 'This is question 9!', 'session_id' => '1'],
            ['question_text' => 'This is question 10!', 'session_id' => '1'],
            ['question_text' => 'This is question 11!', 'session_id' => '1'],
            ['question_text' => 'This is question 12!', 'session_id' => '1']
        ]);

        // and now add the answers
        DB::table('answers')->insert([
            ['answer_text' => 'yes', 'correct' => '1', 'question_id' => '1'],
            ['answer_text' => 'no', 'correct' => '0', 'question_id' => '1'],
            ['answer_text' => 'yes', 'correct' => '1', 'question_id' => '1'],
            ['answer_text' => 'yes', 'correct' => '1', 'question_id' => '1'],

            ['answer_text' => 'yes', 'correct' => '1', 'question_id' => '2'],
            ['answer_text' => 'no', 'correct' => '0', 'question_id' => '2'],
            ['answer_text' => 'no', 'correct' => '0', 'question_id' => '2'],
            ['answer_text' => 'no', 'correct' => '0', 'question_id' => '2'],

            ['answer_text' => 'no', 'correct' => '0', 'question_id' => '3'],
            ['answer_text' => 'no', 'correct' => '0', 'question_id' => '3'],
            ['answer_text' => 'yes', 'correct' => '1', 'question_id' => '3'],
            ['answer_text' => 'yes', 'correct' => '1', 'question_id' => '3'],

            ['answer_text' => 'yes', 'correct' => '1', 'question_id' => '4'],
            ['answer_text' => 'yes', 'correct' => '1', 'question_id' => '4'],
            ['answer_text' => 'yes', 'correct' => '1', 'question_id' => '4'],
            ['answer_text' => 'yes', 'correct' => '1', 'question_id' => '4'],

            ['answer_text' => 'yes', 'correct' => '1', 'question_id' => '5'],
            ['answer_text' => 'no', 'correct' => '0', 'question_id' => '5'],
            ['answer_text' => 'yes', 'correct' => '1', 'question_id' => '5'],
            ['answer_text' => 'no', 'correct' => '0', 'question_id' => '5'],

            ['answer_text' => 'yes', 'correct' => '1', 'question_id' => '6'],
            ['answer_text' => 'no', 'correct' => '0', 'question_id' => '6'],
            ['answer_text' => 'no', 'correct' => '0', 'question_id' => '6'],
            ['answer_text' => 'yes', 'correct' => '1', 'question_id' => '6'],

            ['answer_text' => 'no', 'correct' => '0', 'question_id' => '7'],
            ['answer_text' => 'yes', 'correct' => '1', 'question_id' => '7'],
            ['answer_text' => 'yes', 'correct' => '1', 'question_id' => '7'],
            ['answer_text' => 'yes', 'correct' => '1', 'question_id' => '7'],

            ['answer_text' => 'no', 'correct' => '0', 'question_id' => '8'],
            ['answer_text' => 'no', 'correct' => '0', 'question_id' => '8'],
            ['answer_text' => 'no', 'correct' => '0', 'question_id' => '8'],
            ['answer_text' => 'yes', 'correct' => '1', 'question_id' => '8'],

            ['answer_text' => 'yes', 'correct' => '1', 'question_id' => '9'],
            ['answer_text' => 'no', 'correct' => '0', 'question_id' => '9'],
            ['answer_text' => 'no', 'correct' => '0', 'question_id' => '9'],
            ['answer_text' => 'no', 'correct' => '0', 'question_id' => '9'],

            ['answer_text' => 'yes', 'correct' => '1', 'question_id' => '10'],
            ['answer_text' => 'no', 'correct' => '0', 'question_id' => '10'],
            ['answer_text' => 'no', 'correct' => '0', 'question_id' => '10'],
            ['answer_text' => 'yes', 'correct' => '1', 'question_id' => '10'],

            ['answer_text' => 'no', 'correct' => '0', 'question_id' => '11'],
            ['answer_text' => 'no', 'correct' => '0', 'question_id' => '11'],
            ['answer_text' => 'yes', 'correct' => '1', 'question_id' => '11'],
            ['answer_text' => 'no', 'correct' => '0', 'question_id' => '11'],

            ['answer_text' => 'no', 'correct' => '0', 'question_id' => '12'],
            ['answer_text' => 'yes', 'correct' => '1', 'question_id' => '12'],
            ['answer_text' => 'no', 'correct' => '0', 'question_id' => '12'],
            ['answer_text' => 'no', 'correct' => '0', 'question_id' => '12']
        ]);
    }
}
