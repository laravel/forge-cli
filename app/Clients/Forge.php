<?php

namespace App\Clients;

use App\Support\Panic;
use Laravel\Forge\Forge as BaseForge;
use Psr\Http\Message\ResponseInterface;

class Forge extends BaseForge
{
    /**
     * Number of seconds a request is retried.
     *
     * @var int
     */
    public $timeout = 60;

    /**
     * Get the collection of servers.
     *
     * @return \Laravel\Forge\Resources\Server[]
     */
    public function servers()
    {
        return collect(parent::servers())->filter(function ($server) {
            return $server->revoked == false;
        })->values()->all();
    }

    /**
     * Get the server logs.
     *
     * @param  string|int  $serverId
     * @param  string  $type
     * @return object
     */
    public function logs($serverId, $type)
    {
        return (object) $this->get("servers/$serverId/logs?file=$type");
    }

    /**
     * Get the site logs.
     *
     * @param  string|int  $serverId
     * @param  string|int  $siteId
     * @return object
     */
    public function siteLogs($serverId, $siteId)
    {
        return (object) $this->get("servers/$serverId/sites/$siteId/logs");
    }

    /**
     * Get the site deployments.
     *
     * @param  string|int  $serverId
     * @param  string|int  $siteId
     * @return array
     */
    public function siteDeployments($serverId, $siteId)
    {
        return $this->get("servers/$serverId/sites/$siteId/deployment-history")['deployments'];
    }

    /**
     * Get a site deployment.
     *
     * @param  string|int  $serverId
     * @param  string|int  $siteId
     * @param  string|int  $deploymentId
     * @return object
     */
    public function siteDeployment($serverId, $siteId, $deploymentId)
    {
        return (object) $this->get("servers/$serverId/sites/$siteId/deployment-history/$deploymentId")['deployment'];
    }

    /**
     * Get the site deployment output.
     *
     * @param  string|int  $serverId
     * @param  string|int  $siteId
     * @param  string|int  $deploymentId
     * @return string
     */
    public function siteDeploymentOutput($serverId, $siteId, $deploymentId)
    {
        return $this->get("servers/$serverId/sites/$siteId/deployment-history/$deploymentId/output")['output'];
    }

    /**
     * Handle the request error.
     *
     * @param  \Psr\Http\Message\ResponseInterface  $response
     * @return void
     */
    protected function handleRequestError(ResponseInterface $response)
    {
        if ($response->getStatusCode() >= 500) {
            Panic::abort($response->getBody());
        }

        if ($response->getStatusCode() == 422) {
            $errors = json_decode((string) $response->getBody());

            abort(1, collect($errors)->flatten()->first());
        }

        parent::handleRequestError($response);
    }
}
