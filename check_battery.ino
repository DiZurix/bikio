//Definition de pin
const int BATTERYPIN = A0; //Pin de la batterie

const float TensionMax = 2.4; //Tension maximale de notre batterie
const float TensionMin = 1.4; //Tension minimale de notre batterie

int getValBattery(){
  float b = analogRead(BATTERYPIN); //On récupère la valeur analogique de notre batterie

  float minValue = (1023 * TensionMin) / 5; //Valeur maximale de la tension pour l'Arduino
  float maxValue = (1023 * TensionMax) / 5; //Valeur minimale de la tension pour l'Arduino

  //int minValue = (1023 * TensionMin) / 3.3; //Valeur maximale de la tension pour LORAWAN
  //int maxValue = (1023 * TensionMax) / 3.3; //Valeur minimale de la tension pour LORAWAN

  b = ((b - minValue) / (maxValue - minValue)) * 100; //Conversion en pourcentage

  if (b > 100){ //Si valeur est supérieure à 100, batterie pleine.
    b = 100;
  }
  else if (b < 0){ //Si valeur est inférieure à 0, batterie vide.
    b = 0;
  }
  return b; //On retourne le pourcentage de charge de la batterie
}

//1023 correspond à la découpe en part égale de notre tension.