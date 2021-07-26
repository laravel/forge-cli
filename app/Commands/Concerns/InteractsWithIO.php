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
     * Prompt the user for an "site" input.
     *
     * @param  string  $question
     * @return string|int
     */
    public function askForSite($question)
    {
        $name = $this->argument('site');

        $answers = collect($this->forge->sites($this->currentServer()->id));

        if (is_null($name)) {
            $name = $this->choiceStep($question, $answers->mapWithKeys(function ($resource) {
                return [$resource->id => $resource->name];
            })->all());
        }

        return optional($answers->where('name', $name)->first())->id ?: $name;
    }

    /**
     * Prompt the user for an "server" input.
     *
     * @param  string  $question
     * @return string|int
     */
    public function askForServer($question)
    {
        $name = $this->argument('server');

        $answers = collect($this->forge->servers());

        if (is_null($name)) {
            $name = $this->choiceStep($question, $answers->mapWithKeys(function ($resource) {
                return [$resource->id => $resource->name];
            })->all());
        }

        return optional($answers->where('name', $name)->first())->id ?: $name;
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

    /**
     * Display a ask "step" message.
     *
     * @param  string  $question
     * @param  string|null  $default
     * @return mixed
     */
    public function askStep($question, $default = null)
    {
        $question = ucwords($question);

        return $this->ask('<fg=yellow>‣</> <options=bold>'.$question.'</>', $default);
    }

    /**
     * Display a ask "step" message.
     *
     * @param  string  $question
     * @param  array  $choices
     * @param  string|null  $default
     * @return string|array
     */
    public function choiceStep($question, $choices, $default = null)
    {
        $question = ucwords($question);

        return $this->choice('<fg=yellow>‣</> <options=bold>'.$question.'</>', $choices, $default);
    }
}
