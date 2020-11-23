const int RED_LED = 11;
const int GREEN_LED = 10;

int SWITCH_LED (const int LED){
    digitalWrite(LED, HIGH);
    delay(1500);
    digitalWrite(LED, LOW);
}
