<?php

namespace App\Enums;

enum RoomStatusEnum:string
{
    case OCCUPIED = 'occupied';
    case AVAILABLE = 'available';
}
