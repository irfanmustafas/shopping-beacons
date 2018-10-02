# Shopping Beacons
Shopping Beacons give small businesses the ability to quickly create a simple online presence and broadcast a hyperlocal advert to attract nearby potential customers.

This repository demonstrates how a basic version of such a platform could be created to automatically appear as a notification on nearby Android phones, with no extra app required.

See the [toolkit website](http://digitalinclusiontoolkit.org/) and [research project website](http://www.reshapingthefuture.org/) for more details and related work.

## Setup
1. Copy the contents of this repository to a PHP web server that is accessible over HTTPS, making sure that the folder named in `$uploadsLocation` in `common.php` (default: `"uploads"`) exists and is writable.

2. Set up your beacons to broadcast shop URLs from your newly-created website. For each beacon, in each of the physical locations that are being advertised:

    2.1. Use the [URL validator](http://verify.physical-web.org/) to verify that your shop link will display properly in Google Nearby. URLs **must** be accessible using HTTPS.

    2.2. Press the button on the beacon. If the beacon's LED begins to blink in blue, continue to step 2.3. If the light flashes red and then disappears then you have turned the beacon off - press the button again to turn it on.
  
    2.3. Connect to your beacon by scanning using the [configuration tool](http://cf.physical-web.org/). This step must be done while the beacon's light is blinking in blue. If the beacon's light is not on, go back to step 2.2 to retry.
    
    2.4. Save the shop's URL to the beacon (for example, `https://myshop.photo/?shop=1`), and disconnect, making sure the beacon's light has stopped blinking.
    
    2.5. Place the beacon in a secure place (for example, out of reach on an internal wall; under a counter top, etc). Check that the beacon appears in Google Nearby on any Android phone.

3. When required, the business owner can now visit the beacon webpage to upload a new photo, or change the advert text of the shop. Titles and photos can be updated as often as required, but note that changes will take 5-10 minutes to be reflected in the beacon notifications in [Google Nearby](https://support.google.com/accounts/answer/6260286?hl=en).

Note: In the sample website [myshop.photo](https://myshop.photo/) and this repository we have used [Physical Web](https://google.github.io/physical-web/) beacons. The beacon configuration instructions above (steps 2.1 to 2.4) apply to those devices, but other manufacturers' devices may have different setup methods. If experiencing problems, the [nRF Connect app](https://play.google.com/store/apps/details?id=no.nordicsemi.android.mcp) is a useful tool for beacon setup, and is able to configure various types of BLE devices.

## License
Apache 2.0
