<?php
declare(strict_types=1);

namespace App\Enum;

use MyCLabs\Enum\Enum;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * List des types possibles de présence de professionnel·le
 */
class PresenceProType extends Enum
{

    const PRESENT = 'PRESENT';

    const ABSENT = 'ABSENT';

}