<?php

use App\Support\TtyReader;

it('returns the reader output unchanged when canonical mode cannot be disabled', function () {
    $reader = new class extends TtyReader
    {
        public int $captureCalls = 0;

        public int $disableCalls = 0;

        public int $restoreCalls = 0;

        protected function shouldDisableCanonicalMode()
        {
            return false;
        }

        protected function captureState()
        {
            $this->captureCalls++;

            return null;
        }

        protected function disableCanonicalMode()
        {
            $this->disableCalls++;
        }

        protected function restoreState($state)
        {
            $this->restoreCalls++;
        }
    };

    $result = $reader->read(fn () => 'hello-token');

    expect($result)->toBe('hello-token')
        ->and($reader->captureCalls)->toBe(0)
        ->and($reader->disableCalls)->toBe(0)
        ->and($reader->restoreCalls)->toBe(0);
});

it('toggles canonical mode around the read and always restores state', function () {
    $reader = new class extends TtyReader
    {
        public array $events = [];

        protected function shouldDisableCanonicalMode()
        {
            $this->events[] = 'check';

            return true;
        }

        protected function captureState()
        {
            $this->events[] = 'capture';

            return 'previous-state';
        }

        protected function disableCanonicalMode()
        {
            $this->events[] = 'disable';
        }

        protected function restoreState($state)
        {
            $this->events[] = "restore:{$state}";
        }
    };

    $result = $reader->read(function () use ($reader) {
        $reader->events[] = 'read';

        return 'token-value';
    });

    expect($result)->toBe('token-value')
        ->and($reader->events)->toBe([
            'check',
            'capture',
            'disable',
            'read',
            'restore:previous-state',
        ]);
});

it('restores terminal state even when the reader throws', function () {
    $reader = new class extends TtyReader
    {
        public bool $restored = false;

        protected function shouldDisableCanonicalMode()
        {
            return true;
        }

        protected function captureState()
        {
            return 'previous-state';
        }

        protected function disableCanonicalMode()
        {
            //
        }

        protected function restoreState($state)
        {
            $this->restored = true;
        }
    };

    expect(fn () => $reader->read(function () {
        throw new RuntimeException('boom');
    }))->toThrow(RuntimeException::class, 'boom');

    expect($reader->restored)->toBeTrue();
});

it('skips the toggle when state cannot be captured', function () {
    $reader = new class extends TtyReader
    {
        public int $disableCalls = 0;

        public int $restoreCalls = 0;

        protected function shouldDisableCanonicalMode()
        {
            return true;
        }

        protected function captureState()
        {
            return null;
        }

        protected function disableCanonicalMode()
        {
            $this->disableCalls++;
        }

        protected function restoreState($state)
        {
            $this->restoreCalls++;
        }
    };

    expect($reader->read(fn () => 'token'))->toBe('token')
        ->and($reader->disableCalls)->toBe(0)
        ->and($reader->restoreCalls)->toBe(0);
});
