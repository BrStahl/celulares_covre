
/** Adicionar referência para a tabela CORPORE..PPESSOA **/
ALTER TABLE linha_aparelho_funcionario ADD corp_pessoa_id INT DEFAULT NULL;

/** Tornar coluna pessoa_id anulável **/
ALTER TABLE linha_aparelho_funcionario ALTER COLUMN pessoa_id INT NULL;

/** Tornar coluna cod_ccusto varchar **/
alter table linha_aparelho_funcionario alter column cod_ccusto varchar(30)

/** Atualizar o código dos funcionários **/
UPDATE LAF 
SET LAF.CORP_PESSOA_ID = PPESSOA.CODIGO
FROM LINHA_APARELHO_FUNCIONARIO LAF
 JOIN CARGOSOL..PESSOA WITH (NOLOCK) ON
	PESSOA.PESSOA_ID = LAF.PESSOA_ID
JOIN CORPORE..PPESSOA WITH (NOLOCK) ON
	PPESSOA.CPF = PESSOA.PF_CPF COLLATE SQL_LATIN1_GENERAL_CP1_CI_AI