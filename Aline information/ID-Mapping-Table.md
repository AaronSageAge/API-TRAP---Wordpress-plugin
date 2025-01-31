    

        "Id": 30630,
        "Form": "Sales and Marketing",
        "Name": "Source Type",
        "Label": "FACIL [Stop using this field as of 12.1.23, per Jay and Ramona]",
        "Type": "Select Basic",
        "Required": false,
        "ColumnId": 126,
        "Options": [
          { "Id": 478, "Name": "Ten Twenty Grove" },
          { "Id": 479, "Name": "Lake Forest Place" },
          { "Id": 480, "Name": "Westminster Place" },
          { "Id": 481, "Name": "The Moorings of Arlington Heights" }
        ]
--------------------------------------------

        "Id": 30396,
        "Form": "Default",
        "Name": "PrimaryContact",
        "Label": "Primary Contact",
        "Type": "SelectBasic",
        "Required": false,
        "ColumnId": 90,
        "Options": [
          { "Id": 1, "Name": "Yes" }, //prospect 1 or 2 based on Family member 
          { "Id": 2, "Name": "No" } //contact if present then 1 id not then nothing
        ]
  --------------------------------------------

      "Id": 30323,
      "Form": "General Information",
      "Name": "FirstName",
      "Label": "First Name",
      "Type": "Text Input",
      "Required": false,
      "ColumnId": 20
--------------------------------------------

      "Id": 30325,
      "Form": "General Information",
      "Name": "LastName",
      "Label": "Last Name",
      "Type": "Text Input",
      "Required": false,
      "ColumnId": 22
--------------------------------------------

      "Id": 30326,
      "Form": "General Information",
      "Name": "Phone1",
      "Label": "Home Phone",
      "Type": "Text Input",
      "Required": false,
      "ColumnId": 25
--------------------------------------------

      "Id": 30330,
      "Form": "General Information",
      "Name": "Email",
      "Label": "Email",
      "Type": "Text Input",
      "Required": false,
      "ColumnId": 23
--------------------------------------------

      "Id": 30442,
      "Form": "Sales and Marketing",
      "Name": "Preference",
      "Label": "Care Level",
      "Type": "Select Basic",
      "Required": true,
      "ColumnId": 3,
      "Options": [
        { "Id": 762, "Name": "Assisted Living" },
        { "Id": 763, "Name": "Independent Living" },
        { "Id": 764, "Name": "Memory Care" },
        { "Id": 1366, "Name": "Memory Support" },
        { "Id": 887, "Name": "Rehab" },
        { "Id": 1365, "Name": "Respite" },
        { "Id": 765, "Name": "Skilled Nursing" }
      ]
--------------------------------------------

        "Id": 30463,
        "Form": "Sales and Marketing",
        "Name": "Market Source",
        "Label": "Market Source",
        "Type": "Select Basic",
        "Required": true,
        "ColumnId": 2,
        "Options": [
          { "Id": 40598, "Name": "Adv.Newspaper" },
          { "Id": 21531, "Name": "EMAIL BLAST" },
          { "Id": 21586, "Name": "Referral: FAM/FRIEND" },,
          { "Id": 22322, "Name": "Referral: Other" },
          { "Id": 22332, "Name": "Referral: Professional" },
          { "Id": 22340, "Name": "Referral: Resident" },
          { "Id": 40660, "Name": "Web: Organic Search" },
          { "Id": 40661, "Name": "Web: Programmatic Ads" },
          { "Id": 22518, "Name": "WEBSITE" }
        ]
--------------------------------------------

        "Id": 30367,
        "Form": "Sales and Marketing",
        "Name": "ApartmentPreferenceTypeId",
        "Label": "Apartment Preference",
        "Type": "Select Basic",
        "Required": false,
        "ColumnId": 18,
        "Options": [
          { "Id": 1337, "Name": "Townhouse" },
          { "Id": 1335, "Name": "Cottage" },
          { "Id": 2622, "Name": "Apartments" }
        ]
--------------------------------------------

        "Id": 62908,
        "Form": "UTM Source Mapping",
        "Name": "utmSource",
        "Label": "UTM Source",
        "Type": "Text Input",
        "Required": false
--------------------------------------------

        "Id": 62909,
        "Form": "UTM Source Mapping",
        "Name": "utmMedium",
        "Label": "UTM Medium",
        "Type": "Text Input",
        "Required": false
--------------------------------------------

        "Id": 62910,
        "Form": "UTM Source Mapping",
        "Name": "utmCampaign",
        "Label": "UTM Campaign",
        "Type": "Text Input",
        "Required": false
--------------------------------------------

        "Id": 62911,
        "Form": "UTM Source Mapping",
        "Name": "utmId",
        "Label": "UTM Id",
        "Type": "Text Input",
        "Required": false
--------------------------------------------

        "Id": 62912,
        "Form": "UTM Source Mapping",
        "Name": "gclid",
        "Label": "GCLID",
        "Type": "Text Input",
        "Required": false