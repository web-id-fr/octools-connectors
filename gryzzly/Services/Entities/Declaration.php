<?php

declare(strict_types=1);

namespace Webid\OctoolsGryzzly\Services\Entities;

class Declaration extends \Webid\OctoolsGryzzly\Api\Entities\Declaration
{
    public int $idOctoMember;

    public static function fromDeclaration(
        \Webid\OctoolsGryzzly\Api\Entities\Declaration $declaration,
        int $idOctoMember
    ): self {
        $instance = new self(
            $declaration->id,
            $declaration->duration,
            $declaration->date,
            $declaration->description ?? null,
            $declaration->taskId,
            $declaration->userId,
        );
        $instance->idOctoMember = $idOctoMember;
        return $instance;
    }
}
