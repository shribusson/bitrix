<?

namespace Local\Fwink\Fields\Views\Show;

use Local\Fwink\Fields\Views\Base;

class Client extends Base
{
    protected function getNode()
    {
        $div = $this->dom->createElement('div');
        $div->setAttribute('class', 'client-show-block');

        if ($this->value['COMPANY_URL']) {
            $companyLink = $this->dom->createElement('a', $this->value['COMPANY_TITLE']);
            $companyLink->setAttribute('href', $this->value['COMPANY_URL']);
            $companyLink->setAttribute('title', $this->value['COMPANY_TITLE']);
            $div->appendChild($companyLink);
        } else {
            $companySpan = $this->dom->createElement('span', $this->value['COMPANY_TITLE']);
            $div->appendChild($companySpan);
        }

        if ($this->value['USER_URL']) {
            $userLink = $this->dom->createElement('a', $this->value['USER_NAME']);
            $userLink->setAttribute('href', $this->value['USER_URL']);
            $userLink->setAttribute('title', $this->value['USER_FULL_NAME']);
            $userLink->setAttribute('class', 'client-small');
        } else {
            $userLink = $this->dom->createElement('span', $this->value['USER_NAME']);
            $userLink->setAttribute('class', 'client-small');
        }

        $delimiter = $this->dom->createElement('br');
        $div->appendChild($delimiter);
        $div->appendChild($userLink);

        return $div;
    }

    protected function getDefaultAttributes()
    {
        return [];
    }
}
