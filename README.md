## Usage
  ### Display Results for a Polling Unit
  File: index1.php
  Description: Displays the results of a specific polling unit selected from a dropdown menu.
  ### Display Summed Total Results for an LGA
  File: Ouestion 2_Interface.php
  Description: Displays the summed total results for all polling units under a selected LGA.
  ### Create a New Polling Unit
  File: new_polling_unit.php
  Description: Provides a form to create a new polling unit and store results for multiple parties.
  
## JavaScript Files
  get_wards.php: Fetches wards based on the selected LGA.
  save_results.php: Handles form submission and stores the new polling unit results.
  
## Database Schema
  polling_unit: Stores details about each polling unit.
  announced_pu_results: Stores the results of each polling unit.
  lga: Stores details about each Local Government Area.
  ward: Stores details about each ward.

## Files
  Main Files
  index1.php: Displays individual polling unit results.
  Ouestion 2_Interface.php: Displays summed total results for an LGA.
  new_polling_unit.php: Form for creating a new polling unit.
  save_polling_unit.php: Script to save new polling unit results.
  get_wards.php: Fetches wards based on the selected LGA.
