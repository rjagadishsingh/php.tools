<?php
final class AlignDoubleSlashComments extends AdditionalPass {
	const ALIGNABLE_COMMENT = "\x2 COMMENT%d \x3";
	/**
	 * @codeCoverageIgnore
	 */
	public function candidate($source, $foundTokens) {
		if (isset($foundTokens[T_COMMENT])) {
			return true;
		}
		return false;
	}
	public function format($source) {
		$this->tkns = token_get_all($source);
		$this->code = '';
		$contextCounter = 0;
		while (list($index, $token) = each($this->tkns)) {
			list($id, $text) = $this->getToken($token);
			$this->ptr = $index;
			switch ($id) {
				case T_COMMENT:
					$prefix = '';
					if (substr($text, 0, 2) == '//') {
						$prefix = sprintf(self::ALIGNABLE_COMMENT, $contextCounter);
					}
					$this->appendCode($prefix . $text);

					break;

				case T_WHITESPACE:
					if ($this->hasLn($text)) {
						++$contextCounter;
					}
					$this->appendCode($text);
					break;

				default:
					$this->appendCode($text);
					break;
			}
		}

		$this->alignPlaceholders(self::ALIGNABLE_COMMENT, $contextCounter);

		return $this->code;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function getDescription() {
		return 'Vertically align "//" comments.';
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function getExample() {
		return <<<'EOT'
<?php
//From:
$a = 1; // Comment 1
$bb = 22;  // Comment 2
$ccc = 333;  // Comment 3

//To:
$a = 1;      // Comment 1
$bb = 22;    // Comment 2
$ccc = 333;  // Comment 3

?>
EOT;
	}
}