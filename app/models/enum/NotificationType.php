<?php


namespace models\enum;

abstract class NotificationType extends BasicEnum
{
    const CREATION = "CREATION";
    const UPDATE = "UPDATE";
    const REMOVAL = "REMOVAL";
}