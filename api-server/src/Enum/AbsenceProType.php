<?php
declare(strict_types=1);

namespace App\Enum;

use MyCLabs\Enum\Enum;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * List des types possibles d'absence d'une professionnelle
 */
class AbsenceProType extends Enum
{

    const ABSENT = 'ABSENT';

    const RTT = 'RTT';

    const CP = 'CP';

    const CS = 'CS';

    const CONGE_MALADIE = 'CONGE_MALADIE';

    const ENFANT_MALADE = 'ENFANT_MALADE';

    const OFF = 'OFF';

}