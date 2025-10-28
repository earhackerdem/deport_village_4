<?php

namespace App;

enum TaskStatus: string
{
    case Pendiente = 'pendiente';
    case EnProgreso = 'en progreso';
    case Completada = 'completada';
}
