<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class LoginTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */

    public function testExample()
    {
        $this->browse(function (Browser $first, Browser $sec, Browser $third){
            $first->visit('/login')
                    ->type('email', 'test@test.de')
                    ->type('password', 'qaywsx')
                    ->press('Anmelden')
                    ->assertSee('Session')
                    ->visit('/editorCopySession/6')
                    ->assertSee('Spielstart')
                    ->clickLink('QR-Code anzeigen');

            // Search for our session number, a bit hacky but that way we dont change the html
            $body = $first->text('.wrapperOuterMargin');
            // remove all chars which are not digits and the last digit
            $id = ((int) preg_replace('/[^0-9]/', '', $body))/10;

            $sec->resize(150, 600)
                    ->visit('/mobileStart/' . $id)
                    ->assertSee('Wähle einen Charakter');

            $third->resize(150, 600)
                    ->visit('/mobileStart/' . $id)
                    ->assertSee('Wähle einen Charakter');

            $first->clickLink('Spiel Starten')
                    ->waitForText('2:00', 120)
                    ->press('Spiel abbrechen')
                    ->assertSee('Ihr habt');

            $sec->waitForLocation('/gameFinish')
                    ->assertSee('Deine Auszeichnungen');
        });
    }
}
