<?php

class isbn
{
    private $isbn10 = FALSE; // the stripped ISBN-10, includes given checkdigit
    private $isbn13 = FALSE; // the stripped ISBN-13 (or Bookland EAN), includes given checkdigit
    private $error = ""; // error to return, if required

    private function isbn10_checksum()
    {
        if (strlen($this->isbn10) != 10)
        {
            $this->error = "Given ISBN-10 is not 10 digits (" . $this->isbn10 . ")";
            return FALSE;
        }
        $checksum = 11 - ( ( 10 * substr($this->isbn10,0,1) + 9 * substr($this->isbn10,1,1) + 8 * substr($this->isbn10,2,1) + 7 * substr($this->isbn10,3,1) + 6 * substr($this->isbn10,4,1) + 5 * substr($this->isbn10,5,1) + 4 * substr($this->isbn10,6,1) + 3 * substr($this->isbn10,7,1) + 2 * substr($this->isbn10,8,1) ) % 11);
        /*
         * convert the numeric check value
         * into the single char version
         */
        switch ( $checksum )
        {
            case 10:
                $checksum = "X";
                break;
            case 11:
                $checksum = 0;
                break;
            default:
        }
        return $checksum;
    }
    /***********************************************/

    private function isbn13_checksum()
    {
        if (strlen($this->isbn13) != 13)
        {
            $this->error = "Given ISBN-13 is not 13 digits (" . $this->isbn13 . ")";
            return FALSE;
        }
        /*
         * this checksum calculation could probably be expressed in less
         * space using a loop, but this makes it very clear what the math involved is
         */
        $checksum = 10 - ( ( 1 * substr($this->isbn13,0,1) + 3 * substr($this->isbn13,1,1) + 1 * substr($this->isbn13,2,1) + 3 * substr($this->isbn13,3,1) + 1 * substr($this->isbn13,4,1) + 3 * substr($this->isbn13,5,1) + 1 * substr($this->isbn13,6,1) + 3 * substr($this->isbn13,7,1) + 1 * substr($this->isbn13,8,1) + 3 * substr($this->isbn13,9,1) + 1 * substr($this->isbn13,10,1) + 3 * substr($this->isbn13,11,1) ) % 10 );
        /*
         * convert the numeric check value
         * into the single char version
         */
        if ( $checksum == 10 )
        {
            $checksum = "0";
        }
        return $checksum;
    }
    /***********************************************/

    public function set_isbn10($isbn)
    {
        $isbn = preg_replace("/[^0-9X]/","",strtoupper($isbn)); // strip to the basic ISBN
        if (strlen($isbn)==10)
        {
            $this->isbn10 = $isbn;
        }
        else
        {
            $this->error = "ISBN-10 given is not 10 digits ($isbn)";
            return FALSE;
        }
    }
    /***********************************************/

    public function set_isbn13($isbn)
    {
        $isbn = preg_replace("/[^0-9]/","",strtoupper($isbn)); // strip to the basic ISBN
        if (strlen($isbn)==13)
        {
            $this->isbn13 = $isbn;
        }
        else
        {
            $this->error = "ISBN-13 given is not 13 digits ($isbn)";
            return FALSE;
        }
    }
    /***********************************************/

    public function set_isbn($isbn)
        // trying to provide a common interface here so it's possible to cope
        // if you don't know for sure what you have -- provided the data is valid
    {
        $isbn = preg_replace("/[^0-9X]/","",strtoupper($isbn)); // strip to the basic ISBN
        if (strlen($isbn)==13)
        {
            $this->set_isbn13($isbn);
            return TRUE;
        }
        elseif (strlen($isbn)==10)
        {
            $this->isbn10 = $isbn;
            return TRUE;
        }
        else
        {
            $this->error = "ISBN given is not 10, or 13 digits ($isbn)";
            return FALSE;
        }
    }
    /***********************************************/

    public function isValidISBN10($isbn="")
    {
        if ($isbn != "")
        {
            $this->set_isbn10($isbn);
        }
        if ( FALSE === $this->isbn10 && FALSE !== $this->isbn13 )
        {
            if ( TRUE === $this->isValidISBN13() )
            {
                $this->get_isbn10();
            }
        }
        if ( FALSE === $this->isbn10 || strlen($this->isbn10) != 10 )
        {
            $this->error = "ISBN-10 is not set";
            return FALSE;
        }
        if ( (string) substr($this->isbn10,9,1) === (string) $this->isbn10_checksum() )
        {
            return TRUE;
        }
        else
        {
            $this->error = "Checksum failure";
            return FALSE;
        }
    }
    /***********************************************/

    public function isValidISBN13($isbn="")
    {
        if ($isbn != "") // if we've been given an isbn here, use it
        {
            $this->set_isbn13($isbn);
        }
        if ( FALSE === $this->isbn13 && FALSE !== $this->isbn10 )
        {
            if ( TRUE === $this->isValidISBN10() )
            {
                $this->get_isbn13();
            }
        }
        if ( FALSE === $this->isbn13 || strlen($this->isbn13) != 13 )
        {
            $this->error = "ISBN-13 is not set";
            return FALSE;
        }
        if ( (string) substr($this->isbn13,12,1) === (string) $this->isbn13_checksum() )
        {
            return TRUE;
        }
        else
        {
            $this->error = "Checksum failure";
            return FALSE;
        }
    }
    /***********************************************/

    public function isValidISBN($isbn="")
        // trying to provide a common interface here so it's possible to cope
        // if you don't know for sure what you have -- provided the data is valid
    {
        if ($isbn != "") // if we've been given an ISBN then use it
        {
            $this->set_isbn($isbn);
        }
        if ((isset($this->isbn13) && $this->isValidISBN13() == TRUE) || (isset($this->isbn10) && $this->isValidISBN10() == TRUE) ) // in this routine, we don't care what kind it is, only that it's valid.
        {
            return TRUE;
        }
        else
        {
            $this->error = "Checkdigit failure";
            return FALSE;
        }
    }
    /***********************************************/

    public function get_isbn10()
        // return the ISBN-10 that has been set or create one if we have a valid ISBN-13
    {
        if ( $this->isbn10 != FALSE )
        {
            return $this->isbn10;
        }
        elseif ( $this->isValidISBN13() != FALSE )
        {
            if ( preg_match("/^979/", $this->isbn13) )
            {
                $this->error = "979 Bookland EAN values can't be converted to ISBN-10";
                return FALSE; // if it's a 979 prefix it can't be downgraded
            }
            else
            {
                $this->set_isbn10(substr($this->isbn13, 3, 10)); // invalid ISBN used as a temp value for next step
                $checkdigit = $this->isbn10_checksum();
                $this->set_isbn10(substr($this->isbn13, 3, 9) . $checkdigit); // true value (I hope)
                return $this->isbn10;
            }
        }
        else
        {
            $this->error = "No ISBN-10 value set or calculable";
            return FALSE;
        }
    }
    /*********************************************/

    public function get_isbn13()
        // return the ISBN-13 that has been set or create one if we have a valid ISBN-10
    {
        if ( $this->isbn13 != FALSE )
        {
            return $this->isbn13;
        }
        elseif ( $this->isValidISBN10() != FALSE )
        {
            $this->set_isbn13("978" . substr($this->isbn10, 0, 9) . "0"); // invalid ISBN used as a temp value for next step
            $checkdigit = (string) $this->isbn13_checksum();
            $this->set_isbn13("978" . substr($this->isbn10, 0, 9) . $checkdigit); // true value (I hope)
            return $this->isbn13;
        }
        else
        {
            $this->error = "No ISBN-10 value set or calculable";
            return FALSE;
        }
    }
    /*********************************************/

    public function get_error()
        // return the error message
    {
        return $this->error;
    }
    /*********************************************/

}
