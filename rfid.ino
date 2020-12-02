/**************************************************************************/
/*! 
    @file     readMifare.pde
    @author   Adafruit Industries
	@license  BSD (see license.txt)

    This example will wait for any ISO14443A card or tag, and
    depending on the size of the UID will attempt to read from it.
   
    Si la carte a un UID sur 4 octets, c'est proçbablement une carte Mifare
    Classic. Le cas échéant, suivre ces étapes:
   
    - Authentifier le block 4 (pemier du secteur 1) avec
      la clé par défaut : KEYA 0XFF 0XFF 0XFF 0XFF 0XFF 0XFF
    - Si l'authentification est réussie, on peut maintenant lire les
      4 blocks dans ce secteur (bien que seul le block 4 soit lu ici)
	 
    Si la carte a un UID sur 7 octets, c'est probablement une carte Mifare
    Ultralight , et les pages de 4 octets peuvent être lues directement.
    Page 4 is read by default since this is the first 'general-
    purpose' page on the tags.


Exemple de schéma pour l'Adafruit PN532 NFC/RFID breakout boards
Cette library marche avec le breakout Adafruit NFC 
  ----> https://www.adafruit.com/products/364
 
Check out the links above for our tutorials and wiring diagrams 
These chips use SPI or I2C to communicate.


*/
/**************************************************************************/
#include <Wire.h>
#include <SPI.h>
#include <Adafruit_PN532.h>

//LCD IMPORT

#include <Adafruit_GFX.h>
#include <Adafruit_SSD1306.h>

#define OLED_RESET 4

Adafruit_SSD1306 display(OLED_RESET);

//LCD END IMPORT

// If using the breakout with SPI, define the pins for SPI communication.
#define PN532_SCK  (2)
#define PN532_MOSI (3)
#define PN532_SS   (4)
#define PN532_MISO (5)

// If using the breakout or shield with I2C, define just the pins connected
// to the IRQ and reset lines.  Use the values below (2, 3) for the shield!
#define PN532_IRQ   (2)
#define PN532_RESET (3)  // Not connected by default on the NFC Shield

// Uncomment just _one_ line below depending on how your breakout or shield
// is connected to the Arduino:

// Use this line for a breakout with a software SPI connection (recommended):
//Adafruit_PN532 nfc(PN532_SCK, PN532_MISO, PN532_MOSI, PN532_SS);

// Use this line for a breakout with a hardware SPI connection.  Note that
// the PN532 SCK, MOSI, and MISO pins need to be connected to the Arduino's
// hardware SPI SCK, MOSI, and MISO pins.  On an Arduino Uno these are
// SCK = 13, MOSI = 11, MISO = 12.  The SS line can be any digital IO pin.
//Adafruit_PN532 nfc(PN532_SS);

// Or use this line for a breakout or shield with an I2C connection:
Adafruit_PN532 nfc(PN532_IRQ, PN532_RESET);

const int RED_LED = 11;
const int GREEN_LED = 10;

void setup(void) {
  Serial.begin(115200);
  pinMode(RED_LED, OUTPUT);   //LED
  pinMode(GREEN_LED, OUTPUT); //LED
  display.begin(SSD1306_SWITCHCAPVCC, 0x3C); //LCD
  lcd_logo();

  while (!Serial) delay(10); // for Leonardo/Micro/Zero

  Serial.println("Hello!");

  nfc.begin();

  uint32_t versiondata = nfc.getFirmwareVersion();
  if (! versiondata) {
    Serial.print("Didn't find PN53x board");
    while (1); // halt
  }
  // Got ok data, print it out!
  Serial.print("Found chip PN5"); Serial.println((versiondata>>24) & 0xFF, HEX); 
  Serial.print("Firmware ver. "); Serial.print((versiondata>>16) & 0xFF, DEC); 
  Serial.print('.'); Serial.println((versiondata>>8) & 0xFF, DEC);
  
  // configure board to read RFID tags
  nfc.SAMConfig();
  
  Serial.println("Waiting for an ISO14443A Card ...");
}


void loop(void) {
  uint8_t success;
  uint8_t uid[] = { 0, 0, 0, 0, 0, 0, 0 };  // Buffer to store the returned UID
  uint8_t uidLength;                        // Length of the UID (4 or 7 bytes depending on ISO14443A card type)
    
  // Attente d'une carte type ISO14443A (Mifare, etc.).  Quand une carte est trouvée,
  // 'uid' sera rempli avec le UID, et uidLength indiquera
  // si le IUD est en 4o (Mifare Classic) ou 7o (Mifare Ultralight)
  success = nfc.readPassiveTargetID(PN532_MIFARE_ISO14443A, uid, &uidLength);
  
  if (success) {
    // Affiche les infos de base de la carte
    Serial.println("Found an ISO14443A card");
    Serial.print("  UID Length: ");Serial.print(uidLength, DEC);Serial.println(" bytes");
    Serial.print("  UID Value: ");
    nfc.PrintHex(uid, uidLength);
    Serial.println("");
    
    if (uidLength == 4)
    {
      // On a surement une carte "Mifare Classic"...
      Serial.println("Seems to be a Mifare Classic card (4 byte UID)");
	  
      // On doit maintenant l'authentifier pour accéder aux lecture/écriture
      // Essaie avec la clé par défaut d'usine KeyA: 0xFF 0xFF 0xFF 0xFF 0xFF 0xFF
      Serial.println("Trying to authenticate block 4 with default KEYA value");
      uint8_t keya[6] = { 0xFF, 0xFF, 0xFF, 0xFF, 0xFF, 0xFF };
	  
	  // Essaie avec le block 4 (premier block du secteur 1) depuis secteur 0
	  // contient la data constructeur, mieux vaut ne pas y toucher
	  // à moins que tu saches ce que tu fais.
      success = nfc.mifareclassic_AuthenticateBlock(uid, uidLength, 4, 0, keya);
	  
      if (success)
      {
        Serial.println("Sector 1 (Blocks 4..7) has been authenticated");
        uint8_t data[16];
        int uid_verifnfc[5] = {'b', 'i', 'k', 'i', 'o'};
        int good_uid = 0;
		
        // Pour écrire dans le block 4, tester avec :
		// (ce qui suit devrait être lu en réponse)
              //ECRITURE RFID
        //memcpy(data, (const uint8_t[]){ 'b', 'i', 'k', 'i', 'o', '.', 'u', 's', 'e', 'r', '.', 'a', 'l', 'a', 'i', 'n' }, sizeof data);
        //success = nfc.mifareclassic_WriteDataBlock (4, data);

        // Essaie de lire le contenu du block 4
        success = nfc.mifareclassic_ReadDataBlock(4, data);
		
        if (success)
        {
          // Des données semblent avoir été lues... voyons voir
          Serial.println("Reading Block 4:");
          nfc.PrintHexChar(data, 16);

          for (int i=0; i<5; i++){  //On effectue une boucle afin de vérifier si la carte RFID possède le tag 'bikio'
            if (data[i] == uid_verifnfc[i]){ //On compare la valeur de la carte RFID à la variable initiée précédemment.
              good_uid++; //Si la condition est vraie, on incrémente.
            }
          }
          if (good_uid >= 5){ //S'il possède le tag, on affiche un message de bienvenue et on active la led verte.
            lcd_txt();  //On appelle la fonction qui permettra d'afficher le message de bienvenue.
            SWITCH_LED(GREEN_LED); //On appelle la fonction qui permettra d'allumer la led verte.
          }
          else{ //Sinon, on active la led rouge
            SWITCH_LED(RED_LED);
          }

          // Attendre un peu avant de relire la carte
          delay(1000);
        }
        else
        {
          Serial.println("Ooops ... impossible de lire le block demandé.  Essaie une autre clé ?");
          SWITCH_LED(RED_LED);
        }
      }
      else
      {
        Serial.println("Ooops ... échec de l'authetification. Essayer une autre clé ?");
        SWITCH_LED(RED_LED);
      }
    }
    
    if (uidLength == 7)
    {
      // On a surement une carte Mifare Ultralight...
      Serial.println("On dirait un tag Mifare Ultralight (7 byte UID)");
	  
      // Essai de lecture de la page "à tout faire" utilisateur (#4)
      Serial.println("lecture de la page 4");
      uint8_t data[32];
      success = nfc.mifareultralight_ReadPage (4, data);
      if (success)
      {
        // Des données semblent avoir été lues... voyons voir
        nfc.PrintHexChar(data, 4);
        Serial.println("");
		
        // Attendre un peu avant de relire la carte
        delay(1000);
      }
      else
      {
        Serial.println("Ooops ... impossible de lire la page demandée !?");
      }
    }
  }
  lcd_logo();
}
