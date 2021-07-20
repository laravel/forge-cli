<?php

namespace App\Commands\Concerns;

trait InteractsWithIO
{
    /**
     * Prompt the user for an "id" input.
     *
     * @param  string  $question
     * @param  callable  $answers
     * @return string|int
     */
    public function askForId($question, $answers)
    {
        if (is_null($id = $this->option('id'))) {
            $answers = collect(call_user_func($answers));

            $name = $this->choice($question, $answers->mapWithKeys(function ($resource) {
                return [$resource->id => $resource->name];
            })->all());

            $id = $answers->where('name', $name)->first()->id;
        }

        return $id;
    }

    /**
     * Display a "step" message.
     *
     * @param  string  $text
     * @return void
     */
    public function step($text)
    {
        $this->line('<fg=blue>==></> <options=bold>'.$text.'</>');
    }

    /**
     * Display a successful "step" message.
     *
     * @param  string  $text
     * @return void
     */
    public function successfulStep($text)
    {
        $this->line('<fg=green>==></> <options=bold>'.$text.'</>');
    }
}
