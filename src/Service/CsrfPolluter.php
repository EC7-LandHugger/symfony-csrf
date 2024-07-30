<?php

namespace App\Service;

use App\Enum\Direction;

class CsrfPolluter
{
    private array $alphanumericCharacters;

    private array $keys;

    public function __construct()
    {
        $this->alphanumericCharacters = [
            'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm',
            'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
            'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
            '0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
        ];

        $this->keys = array_flip($this->alphanumericCharacters);
    }

    public function polluteCsrfToken(string $token, Direction $direction): string
    {
        $pollutedToken = '';

        switch ($direction) {
            case Direction::LEFT:
                $nCharsToPolluteOnLeft = $this->calculateNumCharsToPollute($token, Direction::LEFT);
                $pollutedToken = $this->pollute($token, $nCharsToPolluteOnLeft, Direction::LEFT);
                break;
            case Direction::RIGHT:
                $nCharsToPolluteOnRight = $this->calculateNumCharsToPollute($token, Direction::RIGHT);
                $pollutedToken = $this->pollute($token, $nCharsToPolluteOnRight, Direction::RIGHT);
        }

        return $pollutedToken;
    }

    private function calculateNumCharsToPollute($token, Direction $direction): int
    {
        $string = Direction::LEFT === $direction ? $token : strrev($token);

        for ($i = 0; $i < strlen($string); $i++) {
            if (!in_array($string[$i], $this->alphanumericCharacters)) {
                return $i;
            }
        }

        return strlen($string);
    }

    private function pollute(string $token, int $nCharsToPollute, Direction $direction): string
    {
        $pollutedToken = '';

        $string = Direction::LEFT === $direction ? $token : strrev($token);

        // Iterate through each character to be polluted
        for ($i = 0; $i < $nCharsToPollute; $i++) {
            $actualCharacter = $string[$i];

            // Check if the character is in the range of 'a-y', 'A-Y', '0-8'
            if (!preg_match('/[a-yA-Y0-8]/', $actualCharacter)) {
                $pollutedToken .= $actualCharacter;
                continue;
            }

            // Get the index of the actual character
            $index = $this->keys[$actualCharacter];

            // Increment the index and wrap around if necessary
            $index = ($index + 1) % count($this->alphanumericCharacters);

            // Append the new character to the polluted token
            $pollutedToken .= $this->alphanumericCharacters[$index];
        }

        // Append the remaining part of the token
        $pollutedToken .= substr($string, $nCharsToPollute);

        return Direction::LEFT === $direction ? $pollutedToken : strrev($pollutedToken);
    }
}
