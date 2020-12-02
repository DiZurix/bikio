//const int RED_LED = 11;
//const int GREEN_LED = 10;

void SWITCH_LED (const int LED){ //On définit une fonction qui permettra d'allumer la led que l'on désire
    digitalWrite(LED, HIGH);    //On allume la LED (HIGH = Valeur maximal pour l'éclairage)
    delay(1500);                //On met un délai de 1,5 seconde
    digitalWrite(LED, LOW);     //On éteint la LED (LOW = Valeur minimal pour l'éclairage (ETEINT))
}