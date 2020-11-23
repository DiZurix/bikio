const int BATTERYPIN = A0; //Pin de la batterie

const float TensionMax = 4.2; //Tension max
const float TensionMin = 3.2; //Tension min

int getBattery (){
  float b = analogRead(BATTERYPIN); //Valeur analogique

  int minValue = (1023 * TensionMin) / 5; //Arduino
  int maxValue = (1023 * TensionMax) / 5; //Arduino

  //int minValue = (4095 * TensionMin) / 3; //ESP32
  //int maxValue = (4095 * TensionMax) / 3; //ESP32

  b = ((b - minValue) / (maxValue - minValue)) * 100; //Convertion en pourcentage

  if (b > 100) //Si valeur est supérieur à 100, batterie pleine.
    b = 100;

  else if (b < 0) //Si valeur est inférieur à 0, batterie vide.
    b = 0;
  int valeur = b;
  return b;
}