<?php

namespace App\Commands\Concerns;

trait SetsRemoteSshLogin
{
    /**
     * @param mixed $sshOption
     * @return void
     */
    public function setRemoteSshLogin($sshOption)
    {
        if(! is_null($sshOption) && $sshOption !== false) {
            $this->remote->setSshLogin($sshOption);
        }
    }
}
