//Importation des libraires et dépendances
#include <Wire.h>
#include <SPI.h>

#include <Adafruit_PN532.h>

#define PN532_IRQ   (2)
#define PN532_RESET (3)

Adafruit_PN532 nfc(PN532_IRQ, PN532_RESET);

//Definition des pins
const int RED_LED = 11;
const int GREEN_LED = 10;

void setup(void) {
  Serial.begin(115200);

  //Configuration des pins pour les LED
  pinMode(RED_LED, OUTPUT);
  pinMode(GREEN_LED, OUTPUT);

  display.begin(SSD1306_SWITCHCAPVCC, 0x3C);  //Initialisation de l'afficheur LCD
  lcd_logo(); //Affichage du logo sur l'afficheur LCD

  while (!Serial) delay(10); // for Leonardo/Micro/Zero

  Serial.println("Lancement...");

  nfc.begin();

  uint32_t versiondata = nfc.getFirmwareVersion();
  if (!versiondata) { //On vérifie la présence d'une carte RFID
    Serial.print("La carte RFID/NFC PN53x n'a pas été trouvé."); //On envoie un message d'erreur
    while (1); //On effectue une boucle infinie jusqu'à qu'une carte soit detectée
  }
  // La carte RFID est détecté.
  Serial.print("Puce PN5"); Serial.print((versiondata>>24) & 0xFF, HEX); Serial.println(" trouvée.");
  Serial.print("Version du firmware: ");
  Serial.print((versiondata>>16) & 0xFF, DEC); Serial.print('.'); Serial.println((versiondata>>8) & 0xFF, DEC);
  
  // Configuration de la carte pour lire les tags RFID
  nfc.SAMConfig();
  
  Serial.println("En attente d'une carte RFID...");
}


void loop(void) {
  uint8_t success;
  uint8_t uid[] = { 0, 0, 0, 0, 0, 0, 0 };  // Valeur pour stocker l'UID retourné
  uint8_t uidLength;                        // Longueur de l'UID (4 or 7 octects selon le type de carte RFID)
    
  // Attente d'une carte type ISO14443A (Mifare, etc.).  Quand une carte est trouvée,
  // 'uid' sera rempli avec le UID, et uidLength indiquera
  // si le IUD est en 4o (Mifare Classic) ou 7o (Mifare Ultralight)
  success = nfc.readPassiveTargetID(PN532_MIFARE_ISO14443A, uid, &uidLength);
  
  if (success) {
    // Affiche les infos de base de la carte
    Serial.println("Carte RFID trouvée");
    Serial.print("  Taille UID: ");Serial.print(uidLength, DEC);Serial.println(" octects");
    Serial.print("  Valeur UID: ");
    nfc.PrintHex(uid, uidLength);
    Serial.println("");
    
    if (uidLength == 4) {
      // On a surement une carte "Mifare Classic"...
      Serial.println("Carte Mifare Classic détectée (4 octects UID)");
	  
      // On doit maintenant l'authentifier pour accéder aux lecture/écriture
      // Essaie avec la clé par défaut d'usine KeyA: 0xFF 0xFF 0xFF 0xFF 0xFF 0xFF
      Serial.println("Tentative d'authentification avec le bloc 4 avec une clé KeyA");
      uint8_t keya[6] = { 0xFF, 0xFF, 0xFF, 0xFF, 0xFF, 0xFF };
	  
	    // Essaie avec le block 4 (premier block du secteur 1) depuis secteur 0
	    // contient la data constructeur, mieux vaut ne pas y toucher
	    // à moins que tu saches ce que tu fais.
      success = nfc.mifareclassic_AuthenticateBlock(uid, uidLength, 4, 0, keya);
	  
      if (success) {
        Serial.println("Le secteur 1 (Blocs 4..7) a été authentifié");
        uint8_t data[16];
        int uid_verifnfc[5] = {'b', 'i', 'k', 'i', 'o'}; //Valeur pour stocker l'IUD du tag RFID
        int good_uid = 0; // Valeur qui permet de savoir si le tag RFID est bon
		
        // Pour écrire dans le block 4, tester avec :
        //memcpy(data, (const uint8_t[]){ 'b', 'i', 'k', 'i', 'o', '.', 'u', 's', 'e', 'r', '.', 'a', 'l', 'a', 'i', 'n' }, sizeof data);
        //success = nfc.mifareclassic_WriteDataBlock (4, data);

        // Essaie de lire le contenu du block 4
        success = nfc.mifareclassic_ReadDataBlock(4, data);
		
        if (success) {
          // Des données semblent avoir été lues... voyons voir
          Serial.println("Lecture du bloc 4:");
          nfc.PrintHexChar(data, 16);

          for (int i=0; i<5; i++) {  //On effectue une boucle afin de vérifier si la carte RFID possède le tag 'bikio'
            if (data[i] == uid_verifnfc[i]) { //On compare la valeur de la carte RFID à la variable initiée précédemment.
              good_uid++; //Si la condition est vraie, on incrémente.
            }
          }
          if (good_uid >= 5) { //S'il possède le tag, on affiche un message de bienvenue et on active la led verte.
            Serial.println("Le tag RFID a été reconnu");
            lcd_txt();  //On appelle la fonction qui permettra d'afficher le message de bienvenue.
            SWITCH_LED(GREEN_LED); //On appelle la fonction qui permettra d'allumer la led verte.
          }
          else { //Sinon, on active la led rouge
            Serial.println("Le tag RFID n'a pas été reconnu");
            SWITCH_LED(RED_LED);
          }
          // Attendre un peu avant de relire la carte
          delay(1000);
        }
        else {
          Serial.println("Impossible de lire le block demandé. Essayez une autre clé.");
          SWITCH_LED(RED_LED);
        }
      }
      else {
        Serial.println("Echec de l'authetification. Essayez une autre clé.");
        SWITCH_LED(RED_LED);
      }
    }
    
    if (uidLength == 7) {
      // On a surement une carte Mifare Ultralight...
      Serial.println("On dirait un tag Mifare Ultralight (UID de 7 octects)");
	  
      // Essai de lecture de la page "à tout faire" utilisateur (#4)
      Serial.println("Lecture du bloc 4");
      uint8_t data[32];
      success = nfc.mifareultralight_ReadPage (4, data);
      if (success) {
        // Des données ont été lues.
        nfc.PrintHexChar(data, 4);
        Serial.println("");
    
        delay(1000); // Attendre un peu avant de relire la carte
      }
      else {
        Serial.println("Impossible de lire la page demandée");
      }
    }
  }
  lcd_logo();
}