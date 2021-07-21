<?php

namespace App\Commands\Concerns;

trait InteractsWithIO
{
    /**
     * Format input to textual table.
     *
     * @param  array  $headers
     * @param  \Illuminate\Contracts\Support\Arrayable|array  $rows
     * @param  string  $tableStyle
     * @param  array  $columnStyles
     * @return void
     */
    public function table($headers, $rows, $tableStyle = 'default', array $columnStyles = [])
    {
        $this->line('');

        parent::table(
            collect($headers)->map(function ($header) {
                return "   <comment>$header</comment>";
            })->all(),
            collect($rows)->map(function ($row) {
                return collect($row)->map(function ($cell) {
                    return "   <options=bold>$cell</>";
                })->all();
            })->all(),
            'compact'
        );

        $this->line('');
    }

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
        $text = ucwords($text);

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
        $text = ucwords($text);

        $this->line('<fg=green>==></> <options=bold>'.$text.'</>');
    }
}
