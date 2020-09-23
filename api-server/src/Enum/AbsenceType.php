<?php
declare(strict_types=1);

namespace App\Enum;

use MyCLabs\Enum\Enum;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * List des types possibles d'absence d'un enfant ou d'une professionnelle
 */
class AbsenceType extends Enum
{

    const ABSENT = 'ABSENT';

    // Valeurs d'absence pour les pros //

    const RTT = 'RTT';

    const CP = 'CP';

    const CS = 'CS';

    // Valeurs d'absence pour les pros et enfants //

    const MALADIE = 'MALADIE';

}