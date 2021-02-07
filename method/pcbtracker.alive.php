<?php

//添加属性
$xmldata->addChild('pcbtracker');
$xmldata->pcbtracker->addAttribute('ecenable','1');
$xmldata->pcbtracker->addAttribute('eclimit','0');
$xmldata->pcbtracker->addAttribute('expire','1200');
$xmldata->pcbtracker->addAttribute('limit','0');
$xmldata->pcbtracker->addAttribute('status','0');
$xmldata->pcbtracker->addAttribute('time',time());