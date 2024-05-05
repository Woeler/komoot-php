<?php

declare(strict_types=1);

namespace Woeler\KomootPhp\Enums;

enum Sport: string
{
    case HIKING = 'hike';
    case MOUNTAINEERING = 'mountaineering';
    case BIKE_TOURING = 'touringbicycle';
    case E_BIKE_TOURING = 'e_touringbicycle';
    case MOUNTAIN_BIKING = 'mtb';
    case E_MOUNTAIN_BIKING = 'e_mtb';
    case ROAD_CYCLING = 'racebike';
    case E_ROAD_CYCLING = 'e_racebike';
    case RUNNING = 'jogging';
    case GRAVEL_RIDING = 'mtb_easy';
    case E_GRAVEL_RIDING = 'e_mtb_easy';
    case ENDURO_MOUNTAIN_BIKING = 'mtb_advanced';
    case E_ENDURO_MOUNTAIN_BIKING = 'e_mtb_advanced';
    case ROCK_CLIMBING = 'climbing';
    case DOWNHILL_MOUNTAIN_BIKING = 'downhillbike';
    case UNICYCLING = 'unicycle';
    case CROSS_COUNTRY_SKIING = 'nordic';
    case NORDIC_WALKING = 'nordicwalking';
    case SKATING = 'skaten';
    case ALPINE_SKIING = 'skialpin';
    case SKI_TOURING = 'skitour';
    case SLEDDING = 'sled';
    case SNOWBOARDING = 'snowboard';
    case SNOWSHOEING = 'snowshoe';
    case OTHER = 'other';
}
