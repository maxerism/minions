<?php

namespace Minions\Server;

use Illuminate\Support\Str;
use Laravie\Codex\Security\TimeLimitSignature\Verify;
use Minions\Exceptions\InvalidSignature;
use Minions\Exceptions\InvalidToken;
use Minions\Exceptions\MissingSignature;
use Minions\Exceptions\MissingToken;
use Psr\Http\Message\ServerRequestInterface;

class Message
{
    /**
     * Project ID.
     *
     * @var string
     */
    protected $id;

    /**
     * Project configuration.
     *
     * @var array
     */
    protected $config = [];

    /**
     * The PSR-7 Request.
     *
     * @var \Psr\Http\Message\ServerRequestInterface
     */
    protected $request;

    /**
     * The cached request body.
     *
     * @var string
     */
    protected $body;

    /**
     * Construct a new project request.
     *
     * @param string                                   $id
     * @param array                                    $config
     * @param \Psr\Http\Message\ServerRequestInterface $request
     */
    public function __construct(string $id, array $config, ServerRequestInterface $request)
    {
        $this->id = $id;
        $this->config = $config;
        $this->request = $request;
        $this->body = (string) $request->getBody();
    }

    /**
     * Get the request instance.
     *
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    public function request(): ServerRequestInterface
    {
        return $this->request;
    }

    /**
     * Get the project id.
     *
     * @return string
     */
    public function id(): string
    {
        return $this->id;
    }

    /**
     * Get the request body.
     *
     * @return string
     */
    public function body(): string
    {
        return $this->body;
    }

    /**
     * Validate request token.
     *
     * @return bool
     */
    public function validateRequestToken(): bool
    {
        $projectToken = $this->config['token'] ?? null;

        if (! $this->request->hasHeader('Authorization') || empty($projectToken)) {
            throw new MissingToken();
        } else {
            $header = $this->request->getHeader('Authorization')[0];

            if (Str::startsWith($header, 'Token ')) {
                if (! \hash_equals(Str::substr($header, 6), $projectToken)) {
                    throw new InvalidToken();
                }
            }
        }

        return true;
    }

    /**
     * Validate request signature.
     *
     * @return bool
     */
    public function validateRequestSignature(): bool
    {
        $secret = $this->config['signature'] ?? null;
        $body = \json_encode(\json_decode($this->body(), true));

        if (! $this->request->hasHeader('HTTP_X_SIGNATURE') || empty($secret)) {
            throw new MissingSignature();
        } else {
            $signature = new Verify($secret, 'sha256', $config['signature_expired_in'] ?? 300);

            if (! $signature($body, $this->request->getHeader('HTTP_X_SIGNATURE')[0])) {
                throw new InvalidSignature();
            }
        }

        return true;
    }
}
