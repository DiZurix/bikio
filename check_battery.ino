//Definition de pin
const int BATTERYPIN = A0; //Pin de la batterie

const float TensionMax = 4.2; //Tension maximale de notre batterie
const float TensionMin = 3.2; //Tension minimale de notre batterie

int getValBattery (){
  float b = analogRead(BATTERYPIN); //On récupère la valeur analogique de notre batterie

  int minValue = (1023 * TensionMin) / 5; //Valeur maximale de la tension en Arduino
  int maxValue = (1023 * TensionMax) / 5; //Valeur minimale de la tension en Arduino

  b = ((b - minValue) / (maxValue - minValue)) * 100; //Convertions en pourcentage

  if (b > 100){ //Si valeur est supérieure à 100, batterie pleine.
    b = 100;
  }
  else if (b < 0){ //Si valeur est inférieure à 0, batterie vide.
    b = 0;
  }
  return b; //On retourne le pourcentage de charge de la batterie
}