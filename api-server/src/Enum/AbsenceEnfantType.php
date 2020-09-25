<?php
declare(strict_types=1);

namespace App\Enum;

use MyCLabs\Enum\Enum;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * List des types possibles d'absence d'un enfant
 */
class AbsenceEnfantType extends Enum
{

    const ABSENT = 'ABSENT';

    const MALADIE = 'MALADIE';

    const OFF = 'OFF';

}