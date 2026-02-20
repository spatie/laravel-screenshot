<?php

namespace Spatie\LaravelScreenshot\Enums;

enum WaitUntil: string
{
    case Load = 'load';
    case DomContentLoaded = 'domcontentloaded';
    case NetworkIdle0 = 'networkidle0';
    case NetworkIdle2 = 'networkidle2';
}
