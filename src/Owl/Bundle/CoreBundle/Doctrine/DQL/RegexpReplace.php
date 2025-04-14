<?php

declare(strict_types=1);

namespace Owl\Bundle\CoreBundle\Doctrine\DQL;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\Parser;

final class RegexpReplace extends FunctionNode
{
    public $field = null;
    public $pattern = null;
    public $replacement = null;

    public function getSql(SqlWalker $sqlWalker)
    {
        return sprintf(
            'REGEXP_REPLACE(%s, %s, %s)',
            $this->field->dispatch($sqlWalker),
            $this->pattern->dispatch($sqlWalker),
            $this->replacement->dispatch($sqlWalker)
        );
    }

    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->field = $parser->StringPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->pattern = $parser->StringPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->replacement = $parser->StringPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}
