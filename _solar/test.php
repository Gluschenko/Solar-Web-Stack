<?php
require "Core.php";

Solar\Core::Run();
Solar\Core::Hello();

use Solar\Time;
use Solar\Text;
use Solar\JSON;
use Solar\Debug;
use Solar\SQL;
use Solar\Color;
use Solar\Doc;

echo("<h2>Time</h2>");

$t = Time::Make(11, 12, 2018, 22, 40, 11);

DrawDataTable(["Code", "Value"], [
	["Time::GetDate(time())", Time::GetDate(time())],
	["Time::SetTimezone(0)", Time::SetTimezone(0)],
	["Time::GetSimpleDate(time())", Time::GetSimpleDate(time())],
	["Time::SetTimezone(3)", Time::SetTimezone(3)],
	["Time::GetSimpleDate(time())", Time::GetSimpleDate(time())],
	["Time::GetTimezone()", Time::GetTimezone()],
	["Time::GetDate(time())", Time::GetDate(time())],
	["Time::GetSimpleDate($t)", Time::GetSimpleDate($t)],
	["Time::GetDate($t)", Time::GetDate($t)],
	["Time::GetDate($t, Time::TIME)", Time::GetDate($t, Time::TIME)],
	["Time::GetDate($t, Time::DATE)", Time::GetDate($t, Time::DATE)],
	["Time::GetDate($t, Time::DATE | Time::TIME)", Time::GetDate($t, Time::DATE | Time::TIME)],
	["Time::GetDate($t, Time::DATE | Time::TIME | Time::ADVANCED)", Time::GetDate($t, Time::DATE | Time::TIME | Time::ADVANCED)],
]);

DrawDataTable(["Code", "Value"], [
	["Text::SetLocale(Text::LC_RU)", Text::SetLocale(Text::LC_RU)],
	["Time::GetDate(time())", Time::GetDate(time())],
	["Text::SetLocale(Text::LC_EN)", Text::SetLocale(Text::LC_EN)],
	["Time::GetDate(time())", Time::GetDate(time())],
]);

//

echo("<h2>JSON</h2>");

DrawDataTable(["Code", "Value"], [
	['JSON::Encode(["abc" => "123", "cde" => "456", 0 => 2, 1 => 0])', JSON::Encode(["abc" => "123", "cde" => "456", 0 => 2, 1 => 0])],
	['JSON::Encode(new \Solar\AppCredentials(1, "qwe", "dfg"))', JSON::Encode(new \Solar\VKAppCredentials(1, "qwe", "dfg"))],
	['JSON::Encode(1024)', JSON::Encode(1024)],
]);

//

echo("<h2>Text</h2>");

DrawDataTable(["Code", "Value"], [
	['Text::ToNameCase("qwerty qwerty")', Text::ToNameCase("qwerty qwerty")],
	['Text::ToNameCase("русский русский")', Text::ToNameCase("русский русский")],
	['Text::ToCharArray("qwerty qwerty")', json_encode(Text::ToCharArray("qwerty qwerty"))],
]);

//

echo("<h2>Debug</h2>");


DrawDataTable(["Code", "Value"], [
	['Debug::Log("Test")', Debug::Log("Test")],
	['Debug::Log("Test", Debug::NO_OPTIONS)', Debug::Log("Test", Debug::NO_OPTIONS)],
]);

//
/*
echo("<h2>SQL</h2>");


DrawDataTable(["Code", "Value"], [
	['SQL::Escape("SELECT * FROM `users` WHERE `id` > `id`")', SQL::Escape("SELECT * FROM `users` WHERE `id` > `id`")],
	['SQL::Escape("normal")',  SQL::Escape("normal")],
]);
*/
//

echo("<h2>Colors</h2>");

$colors = [];
for($i = 0; $i < 1; $i += 0.05)
{
	$a = Color::FromHEX("#FF0000");
	$b = Color::FromHEX("#0000FF");
	$c = Color::Lerp($a, $b, $i);

	$a2 = Color::FromHEX("#FF0000");
	$b2 = Color::FromHEX("#00FF00");
	$c2 = Color::LerpLinear($a2, $b2, $i);

	$colors[] = [$i,
		"<div style='background: ".$c."'>".$c." | ".$c->ToHEX()."</div>",
		"<div style='background: ".$c2."'>".$c2." | ".$c2->ToHEX()."</div>"
	];
}

//

DrawDataTable(["Lerp", "HSV", "Linear"], $colors);

//

//

function DrawDataTable($titles, $arr)
{
	$header = "";
	foreach ($titles as $title){
		$header .= "<td><b>".$title."</b></td>";
	}

	echo("<style>table{ margin: 2px; } table, td{ border: solid 1px black; }</style>");
	echo("<table><tr>".$header."</tr>");
	foreach($arr as $el){

		$values = "";
		foreach ($el as $e){
			$values .= "<td>".$e."</td>";
		}

		echo("<tr>".$values."</tr>");
	}
	echo("</table>");
}