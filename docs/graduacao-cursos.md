# Cursos de Graduação

Cadastro auxiliar de cursos de graduação para complementar dados do replicado.

Uso esperado:

- `codcur` e `nomcur` vêm do replicado (`Graduacao::listarCursos()`);
- `codset` é mantido localmente na interface administrativa;
- `nomset` e `nomabvset` são resolvidos no endpoint a partir de `codset`.

## Endpoint de consumo

A API é somente leitura para cursos de graduação e expõe:

- `GET /api/graduacao/cursos`
- `GET /api/graduacao/cursos/{codcur}`

O endpoint usa o middleware `api.password`, com o mesmo comportamento de autenticação do endpoint de mensagens:

- permite acesso com senha via `password` (query) ou header `X-Cadastros-Auxiliares-Password`;
- permite acesso sem senha para usuário web autenticado;
- permite requisição same-origin.

## Retorno

### `GET /api/graduacao/cursos`

Retorna a lista dos cursos de graduação cadastrados localmente:

- `id`: identificador local;
- `codcur`: código do curso no replicado;
- `nomcur`: nome do curso persistido localmente;
- `codset`: código do setor cadastrado localmente;
- `nomset`: nome do setor persistido localmente;
- `nomabvset`: abreviação do setor persistida localmente.

Exemplo:

```json
[
  {
    "id": 1,
    "codcur": 1234,
    "nomcur": "Relações Públicas",
    "codset": 558,
    "nomset": "Departamento de Relações Públicas, Propaganda e Turismo",
    "nomabvset": "CRP"
  }
]
```

### `GET /api/graduacao/cursos/{codcur}`

Retorna um curso específico pelo `codcur`:

- `id`: identificador local;
- `codcur`: código do curso no replicado;
- `nomcur`: nome do curso persistido localmente;
- `codset`: código do setor cadastrado localmente;
- `nomset`: nome do setor persistido localmente;
- `nomabvset`: abreviação do setor persistida localmente.

Exemplo:

```json
{
  "id": 1,
  "codcur": 1234,
  "nomcur": "Relações Públicas",
  "codset": 558,
  "nomset": "Departamento de Relações Públicas, Propaganda e Turismo",
  "nomabvset": "CRP"
}
```

## Possíveis respostas de erro

- `404`: quando `codcur` não está cadastrado localmente em `cursos_graduacao`;
