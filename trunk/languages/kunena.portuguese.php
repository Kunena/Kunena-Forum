<?php
/**
* @version $Id: kunena.english.php 449 2009-02-17 10:55:21Z fxstein $
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2009 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
*
* Based on FireBoard Component
* @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.bestofjoomla.com
*
* Based on Joomlaboard Component
* @copyright (C) 2000 - 2004 TSMF / Jan de Graaff / All Rights Reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author TSMF & Jan de Graaff

*****************************************************************************************
* Traduzido para Português por/Translate for Portuguese by Carlos Flores
* Portugal, 05/04/2009 - 1ª Tradução/1st Translation
* Algumas partes do Bakcend não foram traduzidos/Some parts of backend are not translate
* Todas as partes do Frontend foram traduzidas/All parts of Frontend are translate
* Revisão 1.0.9
* ***************************************************************************************
**/

// Dont allow direct linking
defined( '_JEXEC' ) or defined ('_VALID_MOS') or die('Direct Access to this location is not allowed.');

// 1.0.9
DEFINE('_KUNENA_INSTALLED_VERSION', 'Versão instalada');
DEFINE('_KUNENA_COPYRIGHT', 'Copyright');
DEFINE('_KUNENA_LICENSE', 'Licença');
DEFINE('_KUNENA_PROFILE_NO_USER', 'Utilizador inexistente.');
DEFINE('_KUNENA_PROFILE_NOT_FOUND', 'O Utilizador ainda não visitou o Fórum, não tem Perfil de utilizador.');

// Search
DEFINE('_KUNENA_SEARCH_RESULTS', 'Resultados da procura');
DEFINE('_KUNENA_SEARCH_ADVSEARCH', 'Procura avançada');
DEFINE('_KUNENA_SEARCH_SEARCHBY_KEYWORD', 'Procurar por palavra');
DEFINE('_KUNENA_SEARCH_KEYWORDS', 'Palavras');
DEFINE('_KUNENA_SEARCH_SEARCH_POSTS', 'Procurar Posts inteiros');
DEFINE('_KUNENA_SEARCH_SEARCH_TITLES', 'Procurar só os títulos');
DEFINE('_KUNENA_SEARCH_SEARCHBY_USER', 'Procurar por nome de utilizador');
DEFINE('_KUNENA_SEARCH_UNAME', 'Nome de utilizador');
DEFINE('_KUNENA_SEARCH_EXACT', 'Nome real');
DEFINE('_KUNENA_SEARCH_USER_POSTED', 'Mensagens colocadas por');
DEFINE('_KUNENA_SEARCH_USER_STARTED', 'Tópicos iniciados por');
DEFINE('_KUNENA_SEARCH_USER_ACTIVE', 'Actividade em tópicos');
DEFINE('_KUNENA_SEARCH_OPTIONS', 'Opções de procura');
DEFINE('_KUNENA_SEARCH_FIND_WITH', 'Encontrar tópicos com');
DEFINE('_KUNENA_SEARCH_LEAST', 'Pelo menos');
DEFINE('_KUNENA_SEARCH_MOST', 'O mais possível');
DEFINE('_KUNENA_SEARCH_ANSWERS', 'Respostas');
DEFINE('_KUNENA_SEARCH_FIND_POSTS', 'Encontrar Post de');
DEFINE('_KUNENA_SEARCH_DATE_ANY', 'Qualquer data');
DEFINE('_KUNENA_SEARCH_DATE_LASTVISIT', 'Última visita');
DEFINE('_KUNENA_SEARCH_DATE_YESTERDAY', 'Ontem');
DEFINE('_KUNENA_SEARCH_DATE_WEEK', 'Uma semana atrás');
DEFINE('_KUNENA_SEARCH_DATE_2WEEKS', '2 semanas atrás');
DEFINE('_KUNENA_SEARCH_DATE_MONTH', 'Há 1 mês atrás');
DEFINE('_KUNENA_SEARCH_DATE_3MONTHS', '3 meses atrás');
DEFINE('_KUNENA_SEARCH_DATE_6MONTHS', '6 meses atrás');
DEFINE('_KUNENA_SEARCH_DATE_YEAR', 'Há 1 anoa atrás');
DEFINE('_KUNENA_SEARCH_DATE_NEWER', 'Mais recente');
DEFINE('_KUNENA_SEARCH_DATE_OLDER', 'Mais antigo');
DEFINE('_KUNENA_SEARCH_SORTBY', 'Ordenar resultados por');
DEFINE('_KUNENA_SEARCH_SORTBY_TITLE', 'Título');
DEFINE('_KUNENA_SEARCH_SORTBY_POSTS', 'Número de posts');
DEFINE('_KUNENA_SEARCH_SORTBY_VIEWS', 'Número de visualizações');
DEFINE('_KUNENA_SEARCH_SORTBY_START', 'Data de início do tópico');
DEFINE('_KUNENA_SEARCH_SORTBY_POST', 'Data de colocação');
DEFINE('_KUNENA_SEARCH_SORTBY_USER', 'Nome de utilizador');
DEFINE('_KUNENA_SEARCH_SORTBY_FORUM', 'Fórum');
DEFINE('_KUNENA_SEARCH_SORTBY_INC', 'Ordem Ascendente');
DEFINE('_KUNENA_SEARCH_SORTBY_DEC', 'Ordem Descendente');
DEFINE('_KUNENA_SEARCH_START', 'Avançar para os resultados');
DEFINE('_KUNENA_SEARCH_LIMIT5', 'Mostrar 5 Resultados');
DEFINE('_KUNENA_SEARCH_LIMIT10', 'Mostrar 10 Resultados');
DEFINE('_KUNENA_SEARCH_LIMIT15', 'Mostrar 15 Resultados');
DEFINE('_KUNENA_SEARCH_LIMIT20', 'Mostrar 20 Resultados');
DEFINE('_KUNENA_SEARCH_SEARCHIN', 'Procurar nas Categorias');
DEFINE('_KUNENA_SEARCH_SEARCHIN_ALLCATS', 'Todas as Categorias');
DEFINE('_KUNENA_SEARCH_SEARCHIN_CHILDREN', 'Procurar também nos sub-fóruns');
DEFINE('_KUNENA_SEARCH_SEND', 'Enviar');
DEFINE('_KUNENA_SEARCH_CANCEL', 'Cancelar');
DEFINE('_KUNENA_SEARCH_ERR_NOPOSTS', 'Não foram encontrados resultados para todos os termos da sua procura.');
DEFINE('_KUNENA_SEARCH_ERR_SHORTKEYWORD', 'Pelo menos uma das palavras a procurar deve ter 3 caracteres!');

// 1.0.8
DEFINE('_KUNENA_CATID', 'ID');
DEFINE('_POST_NOT_MODERATOR', 'Não tem permissões de moderação!');
DEFINE('_POST_NO_FAVORITED_TOPIC', 'Este tópico <b>NÃO</b> foi adicionado aos seus favoritos');
DEFINE('_COM_C_SYNCEUSERSDESC', 'Sincronizar a tabela de utilizador Kunena com a tabela de utilizador Joomla');
DEFINE('_POST_FORGOT_EMAIL', 'Esqueceu-se de mencionar o seu endereço de e-mail.  Clique no botão retroceder no seu browser&#146s e tente de novo.');
DEFINE('_KUNENA_POST_DEL_ERR_FILE', 'Tudo apagado. Alguns ficheiros anexos foram perdidos!');
// New strings for initial forum setup. Replacement for legacy sample data
DEFINE('_KUNENA_SAMPLE_FORUM_MENU_TITLE', 'Fórum');
DEFINE('_KUNENA_SAMPLE_MAIN_CATEGORY_TITLE', 'Fórum Principal');
DEFINE('_KUNENA_SAMPLE_MAIN_CATEGORY_DESC', 'Esta é a categoria principal do fórum. Como categoria serve para conter os painéis ou fóruns individuais que forem desenvolvidos sobre ele. É o 1º nível da categoria e é sempre necessário para configurar o Fórum Kunena.');
DEFINE('_KUNENA_SAMPLE_MAIN_CATEGORY_HEADER', 'Por forma a providenciar informação adicional para os seus visitantes e membros, poderá mostrar no cabeçalho do fórum um determinado texto no topo da respectiva categoria.');
DEFINE('_KUNENA_SAMPLE_FORUM1_TITLE', 'Bem vindo Carlos');
DEFINE('_KUNENA_SAMPLE_FORUM1_DESC', 'Encorajar os novos membros a colocar uma pequena introdução acerca deles nesta categoria do fórum. Vá conhecer os outros e partilhar interesses comuns.<br>');
DEFINE('_KUNENA_SAMPLE_FORUM1_HEADER', '[b]Welcome to the Kunena Forum![/b]

Tell us and our members who you are, what you like and why you became a member of this site.
We welcome all new members and hope to see you around a lot!
');
DEFINE('_KUNENA_SAMPLE_FORUM2_TITLE', 'Caixa de Sugestões');
DEFINE('_KUNENA_SAMPLE_FORUM2_DESC', 'Have some feedback and input to share?<br>Don\'t be shy and drop us a note. We want to hear from you and strive to make our site better and more user friendly for our guests and members a like.');
DEFINE('_KUNENA_SAMPLE_FORUM2_HEADER', 'Este é um cabeçalho opcional para a Caixa de Sugestões do Fórum.<br>');
DEFINE('_KUNENA_SAMPLE_POST1_SUBJECT', 'Bem vindo ao Kunena Forum!');
DEFINE('_KUNENA_SAMPLE_POST1_TEXT', '[size=4][b]Bem vindo ao Kunena Forum![/b][/size]

Thank you for choosing Kunena for your community forum needs in Joomla.

Kunena, translated from Swahili meaning "to speak", is built by a team of open source professionals with the goal of providing a top-quality, tightly unified forum solution for Joomla. Kunena even supports social networking components like Community Builder and JomSocial.


[size=4][b]Additional Kunena Resources[/b][/size]

[b]Kunena Documentation:[/b] http://www.kunena.com/docs
(http://docs.kunena.com)

[b]Kunena Support Forum[/b]: http://www.kunena.com/forum
(http://www.kunena.com/index.php?option=com_kunena&Itemid=125)

[b]Kunena Downloads:[/b] http://www.kunena.com/downloads
(http://joomlacode.org/gf/project/kunena/frs/)

[b]Kunena Blog:[/b] http://www.kunena.com/blog
(http://www.kunena.com/index.php?option=com_content&view=section&layout=blog&id=7&Itemid=128)

[b]Submit your feature ideas:[/b] http://www.kunena.com/uservoice
(http://kunena.uservoice.com/pages/general?referer_type=top3)

[b]Follow Kunena on Twitter:[/b] http://www.kunena.com/twitter
(https://twitter.com/kunena)');

// 1.0.6
DEFINE('_KUNENA_JOMSOCIAL', 'JomSocial');

// 1.0.5
DEFINE('_COM_A_HIGHLIGHTCODE', 'Activar Realçamento de Código');
DEFINE('_COM_A_HIGHLIGHTCODE_DESC', 'Activa o java script de realçamento da tag código do Kunena. Se os seus Utilizadores publicarem código php ou fragmentos semelhantes dentro das tags código, activando isto irá colorir o código. Se o seu fórum não faz uso destas publicações de linguagem de programação, pode querer desactivá-lo para evitar que as tags código sejam mal formadas.');
DEFINE('_COM_A_RSS_TYPE', 'Tipo Padrão de RSS');
DEFINE('_COM_A_RSS_TYPE_DESC', 'Escolher entre os RSS feeds &quot;Por tópico &quot; ou &quot;Por Colocação&quot; &quot;Por tópico&quot; significa que apenas uma entrada por tópico será listada no feed RSS, independentemente de quantas publicações foram feitas dentro desse segmento. &quot;Por colocação&quot; cria um feed RSS mais curto e compacto, mas não irá listar todas as respostas feitas.');
DEFINE('_COM_A_RSS_BY_THREAD', 'Por tópico');
DEFINE('_COM_A_RSS_BY_POST', 'Por Post');
DEFINE('_COM_A_RSS_HISTORY', 'Histórico RSS');
DEFINE('_COM_A_RSS_HISTORY_DESC', 'Seleccione quanto do histórico deverá ser incluído no feed RSS. O padrão é de 1 mês, mas pode querer limitá-lo a 1 semana em sites com grande volume.');
DEFINE('_COM_A_RSS_HISTORY_WEEK', '1 Semana');
DEFINE('_COM_A_RSS_HISTORY_MONTH', '1 Mês');
DEFINE('_COM_A_RSS_HISTORY_YEAR', '1 Ano');
DEFINE('_COM_A_FBDEFAULT_PAGE', 'Página padrão do Kunena');
DEFINE('_COM_A_FBDEFAULT_PAGE_DESC', 'Seleccione a página padrão do Kunena que será exibida quando um link for clicado ou o fórum for acedido inicialmente. O padrão é &quot;Debates Recentes&quot;. Deve ser definida para Categorias e para temas diferentes do default_ex. Se &quot;Meus Debates&quot; for seleccionado, visitantes serão direccionados para &quot;Debates Recentes&quot;.');
DEFINE('_COM_A_FBDEFAULT_PAGE_RECENT', 'Debates recentes');
DEFINE('_COM_A_FBDEFAULT_PAGE_MY', 'Os meus Debates');
DEFINE('_COM_A_FBDEFAULT_PAGE_CATEGORIES', 'Categorias');
DEFINE('_KUNENA_BBCODE_HIDE', 'Aos utilizadores que não estejam registados será ocultado o seguinte:');
DEFINE('_KUNENA_BBCODE_SPOILER', 'Atenção: Spoiler!');
DEFINE('_KUNENA_FORUM_SAME_ERR', 'O Fórum Categoria (Pai) não deve ser o mesmo.');
DEFINE('_KUNENA_FORUM_OWNCHILD_ERR', 'O Fórum Categoria (Pai) é um de seus próprios filhos (Child).');
DEFINE('_KUNENA_FORUM_UNKNOWN_ERR', 'ID do Fórum não existe.');
DEFINE('_KUNENA_RECURSION', 'Recursividade detectada.');
DEFINE('_POST_FORGOT_NAME_ALERT', 'Esqueceu-se de colocar o seu nome.');
DEFINE('_POST_FORGOT_EMAIL_ALERT', 'Esqueceu-se de colocar o seu e-mail.');
DEFINE('_POST_FORGOT_SUBJECT_ALERT', 'Esqueceu-se de colocar um assunto.');
DEFINE('_KUNENA_EDIT_TITLE', 'Editar os seus detalhes');
DEFINE('_KUNENA_YOUR_NAME', 'Seu Nome:');
DEFINE('_KUNENA_EMAIL', 'E-mail:');
DEFINE('_KUNENA_UNAME', 'Nome utilizador:');
DEFINE('_KUNENA_PASS', 'Palavra-chave:');
DEFINE('_KUNENA_VPASS', 'Verificar Palavra-chave:');
DEFINE('_KUNENA_USER_DETAILS_SAVE', 'Os detalhes de utilizador foram guardados.');
DEFINE('_KUNENA_TEAM_CREDITS', 'Créditos');
DEFINE('_COM_A_BBCODE', 'Código BBCode');
DEFINE('_COM_A_BBCODE_SETTINGS', 'Configurações código BBCode');
DEFINE('_COM_A_SHOWSPOILERTAG', 'Exibir tag spoiler na barra do editor');
DEFINE('_COM_A_SHOWSPOILERTAG_DESC', 'Escolha &quot;Sim&quot; se quiser que a tag [spoiler] seja exibida na barra do editor de publicação.');
DEFINE('_COM_A_SHOWVIDEOTAG', 'Exibir tag video na barra do editor');
DEFINE('_COM_A_SHOWVIDEOTAG_DESC', 'Escolha &quot;Sim&quot; se quiser que a tag [video] seja listada na barra do editor de publicação.');
DEFINE('_COM_A_SHOWEBAYTAG', 'Exibir tag eBay na barra do editor');
DEFINE('_COM_A_SHOWEBAYTAG_DESC', 'Escolha &quot;Sim&quot; se quer que a tag [ebay] seja listada na barra do editor de publicação.');
DEFINE('_COM_A_TRIMLONGURLS', 'Aparar URLs longas');
DEFINE('_COM_A_TRIMLONGURLS_DESC', 'Escolha &quot;Sim&quot; se quiser que URLs longas sejam aparadas. Veja configurações de aparo de URL no início e no fim.');
DEFINE('_COM_A_TRIMLONGURLSFRONT', 'Porção inicial de URLs aparadas');
DEFINE('_COM_A_TRIMLONGURLSFRONT_DESC', 'Número de caracteres para porção inicial de URLs aparadas. Aparar URLs longas deve ser escolhido para &quot;Sim&quot;.');
DEFINE('_COM_A_TRIMLONGURLSBACK', 'Porção final de URLs aparadas');
DEFINE('_COM_A_TRIMLONGURLSBACK_DESC', 'Número de caracteres para porção final de URLs aparadas. Aparar URLs longas deve ser escolhido para &quot;Sim&quot;.');
DEFINE('_COM_A_AUTOEMBEDYOUTUBE', 'Auto incorporar vídeos do YouTube');
DEFINE('_COM_A_AUTOEMBEDYOUTUBE_DESC', 'Escolha &quot;Sim&quot; se quer que URLs de vídeos do youtube sejam automaticamente incorporados.');
DEFINE('_COM_A_AUTOEMBEDEBAY', 'Auto incorporar itens do eBay');
DEFINE('_COM_A_AUTOEMBEDEBAY_DESC', 'Escolha &quot;Sim&quot; se quer que pesquisas e itens do eBay sejam automaticamente incorporados.');
DEFINE('_COM_A_EBAYLANGUAGECODE', 'Código de idioma do widget do eBay');
DEFINE('_COM_A_EBAYLANGUAGECODE_DESC', 'É importante configurar o código de idioma apropriado porque o widget do eBay deriva a linguagem e moeda dele. O padrão é en-us para ebay.com. Exemplos: ebay.de: de-de, ebay.at: de-at, ebay.co.uk: en-gb');
DEFINE('_COM_A_KUNENA_SESSION_TIMEOUT', 'Duração da Sessão');
DEFINE('_COM_A_KUNENA_SESSION_TIMEOUT_DESC', 'O padrão é 1800 [segundos]. Duração da sessão (timeout) em segundos similar à duração da sessão do Joomla. A duração da sessão é importante para recálculo de direitos de accesso, exibição de quem está online e indicador de NOVO. Uma vez que uma sessão expire após este tempo, os direitos de acesso e o indicador de NOVO são reiniciados.');

// Advanced administrator merge-split functions
DEFINE('_GEN_MERGE', 'Juntar');
DEFINE('_VIEW_MERGE', '');
DEFINE('_POST_MERGE_TOPIC', 'Juntar este tópico com');
DEFINE('_POST_MERGE_GHOST', 'Deixar cópia fantasma do tópico');
DEFINE('_POST_SUCCESS_MERGE', 'Junção do tópico efectuada com sucesso.');
DEFINE('_POST_TOPIC_NOT_MERGED', 'Junção falhada.');
DEFINE('_GEN_SPLIT', 'Dividir');
DEFINE('_GEN_DOSPLIT', 'Ir');
DEFINE('_VIEW_SPLIT', '');
DEFINE('_POST_SUCCESS_SPLIT', 'Tópico dividido com sucesso.');
DEFINE('_POST_SUCCESS_SPLIT_TOPIC_CHANGED', 'Tópico alterado com sucesso.');
DEFINE('_POST_SPLIT_TOPIC_NOT_CHANGED', 'Alteração de Tópico falhou.');
DEFINE('_POST_TOPIC_NOT_SPLIT', 'Divisão falhou.');
DEFINE('_POST_DUPLICATE_IGNORED', 'Duplicado. Mensagem idêntica pelo que foi ignorada.');
DEFINE('_POST_SPLIT_HINT', '<br />Sugestão: Pode promover um post para uma posição de tópico se o seleccionar na 2ª coluna e marcar que não pretende dividir.<br />');
DEFINE('_POST_LINK_ORPHANS_TOPIC', 'Linkar órfãos ao tópico');
DEFINE('_POST_LINK_ORPHANS_TOPIC_TITLE', 'Linkar órfãos ao novo post do tópico.');
DEFINE('_POST_LINK_ORPHANS_PREVPOST', 'Linkar órfãos ao post anterior');
DEFINE('_POST_LINK_ORPHANS_PREVPOST_TITLE', 'Linkar órfãos ao post anterior.');
DEFINE('_POST_MERGE', 'juntar');
DEFINE('_POST_MERGE_TITLE', 'Anexar este tópico ao primeiro post.');
DEFINE('_POST_INVERSE_MERGE', 'reverter junção');
DEFINE('_POST_INVERSE_MERGE_TITLE', 'Anexar o primeiro post a este tópico.');

// Additional
DEFINE('_POST_UNFAVORITED_TOPIC', 'Este tópico foi removido dos seus favoritos.');
DEFINE('_POST_NO_UNFAVORITED_TOPIC', 'Este tópico <b>NÃO</b> foi removido dos seus favoritos');
DEFINE('_POST_SUCCESS_UNFAVORITE', 'O seu pedido para remover dos favoritos foi processado.');
DEFINE('_POST_UNSUBSCRIBED_TOPIC', 'Este tópico foi removido das suas subscrições.');
DEFINE('_POST_NO_UNSUBSCRIBED_TOPIC', 'Este tópico <b>NÃO</b> foi removido das suas subscrições');
DEFINE('_POST_SUCCESS_UNSUBSCRIBE', 'O seu pedido para remover das suas subscrições foi processado.');
DEFINE('_POST_NO_DEST_CATEGORY', 'Não foi seleccionada nenhuma categoria. Não foi possível efectuar a movimentação.');
// Default_EX template
DEFINE('_KUNENA_ALL_DISCUSSIONS', 'Debates Recentes');
DEFINE('_KUNENA_MY_DISCUSSIONS', 'Meus Debates');
DEFINE('_KUNENA_MY_DISCUSSIONS_DETAIL', 'Debates que iniciei ou respondi para');
DEFINE('_KUNENA_CATEGORY', 'Categoria:');
DEFINE('_KUNENA_CATEGORIES', 'Categorias');
DEFINE('_KUNENA_POSTED_AT', 'Colocado');
DEFINE('_KUNENA_AGO', 'atrás');
DEFINE('_KUNENA_DISCUSSIONS', 'Debate(s)');
DEFINE('_KUNENA_TOTAL_THREADS', 'Total de tópicos:');
DEFINE('_SHOW_DEFAULT', 'Padrão');
DEFINE('_SHOW_MONTH', 'Mês');
DEFINE('_SHOW_YEAR', 'Ano');

// 1.0.4
DEFINE('_KUNENA_COPY_FILE', 'A copiar "%src%" para "%dst%"...');
DEFINE('_KUNENA_COPY_OK', 'OK');
DEFINE('_KUNENA_CSS_SAVE', 'Guardar aqui o ficheiro CSS...ficheiro="%file%"');
DEFINE('_KUNENA_UP_ATT_10', 'The attachment table was successfully upgraded to the latest 1.0.x series structure!');
DEFINE('_KUNENA_UP_ATT_10_MSG', 'The attachments in the message table were successfully upgraded to the latest 1.0.x series structure!');
DEFINE('_KUNENA_TOPIC_MOVED', '---');
DEFINE('_KUNENA_TOPIC_MOVED_LONG', '------------');
DEFINE('_KUNENA_POST_DEL_ERR_CHILD', 'Could not promote children in post hierarchy. Nothing deleted.');
DEFINE('_KUNENA_POST_DEL_ERR_MSG', 'Impossível apagar o(s) post(s) - nada mais excluído');
DEFINE('_KUNENA_POST_DEL_ERR_TXT', 'Impossível apagar os textos do(s) post(s). Actualize manualmente a base de dados (mesid=%id%).');
DEFINE('_KUNENA_POST_DEL_ERR_USR', 'Tudo Apagado, mas falhou ao actualizar as estatísticas de post do Utilizador!');
DEFINE('_KUNENA_POST_MOV_ERR_DB', "Erro severo na base de dados. Actualize a sua base de dados manualmente, assim as respostas ao tópico serão combinadas com o fórum novo também");
DEFINE('_KUNENA_UNIST_SUCCESS', "O componente Kunena foi desinstalado com sucesso!");
DEFINE('_KUNENA_PDF_VERSION', 'Versão do Componente Kunena Forum: %version%');
DEFINE('_KUNENA_PDF_DATE', 'Gerado em: %date%');
DEFINE('_KUNENA_SEARCH_NOFORUM', 'Não existem fóruns para pesquisar.');

DEFINE('_KUNENA_ERRORADDUSERS', 'Erro ao adicionar utilizadores:');
DEFINE('_KUNENA_USERSSYNCDELETED', 'Utilizadores sincronizados. Apagados:');
DEFINE('_KUNENA_USERSSYNCADD', ', adicionados:');
DEFINE('_KUNENA_SYNCUSERPROFILES', 'perfis de utilizador.');
DEFINE('_KUNENA_NOPROFILESFORSYNC', 'Não foram encontrados perfis elegíveis para sincronizar.');
DEFINE('_KUNENA_SYNC_USERS', 'Sincronização de utilizadores');
DEFINE('_KUNENA_SYNC_USERS_DESC', 'Sincronizar a tabela de utilizador Kunena com a tabela de utilizador Joomla');
DEFINE('_KUNENA_A_MAIL_ADMIN', 'E-mail Administradores');
DEFINE('_KUNENA_A_MAIL_ADMIN_DESC',
    'Escolha &quot;Sim&quot; se pretender receber notificações por email por cada novo post colocado no sistema activado para o(s) administrador(es).');
DEFINE('_KUNENA_RANKS_EDIT', 'Editar Rank');
DEFINE('_KUNENA_USER_HIDEEMAIL', 'Ocultar E-mail');
DEFINE('_KUNENA_DT_DATE_FMT','%d/%m/%Y');
DEFINE('_KUNENA_DT_TIME_FMT','%H:%M');
DEFINE('_KUNENA_DT_DATETIME_FMT','%d/%m/%Y %H:%M');
DEFINE('_KUNENA_DT_LDAY_SUN', 'Domingo');
DEFINE('_KUNENA_DT_LDAY_MON', 'Segunda-feira');
DEFINE('_KUNENA_DT_LDAY_TUE', 'Terça-feira');
DEFINE('_KUNENA_DT_LDAY_WED', 'Quarta-feira');
DEFINE('_KUNENA_DT_LDAY_THU', 'Quinta-feira');
DEFINE('_KUNENA_DT_LDAY_FRI', 'Sexta-feira');
DEFINE('_KUNENA_DT_LDAY_SAT', 'Sábado');
DEFINE('_KUNENA_DT_DAY_SUN', 'DOM');
DEFINE('_KUNENA_DT_DAY_MON', 'SEG');
DEFINE('_KUNENA_DT_DAY_TUE', 'TER');
DEFINE('_KUNENA_DT_DAY_WED', 'QUA');
DEFINE('_KUNENA_DT_DAY_THU', 'QUI');
DEFINE('_KUNENA_DT_DAY_FRI', 'SEX');
DEFINE('_KUNENA_DT_DAY_SAT', 'SÁB');
DEFINE('_KUNENA_DT_LMON_JAN', 'Janeiro');
DEFINE('_KUNENA_DT_LMON_FEB', 'Fevereiro');
DEFINE('_KUNENA_DT_LMON_MAR', 'Março');
DEFINE('_KUNENA_DT_LMON_APR', 'Abril');
DEFINE('_KUNENA_DT_LMON_MAY', 'Maio');
DEFINE('_KUNENA_DT_LMON_JUN', 'Junho');
DEFINE('_KUNENA_DT_LMON_JUL', 'Julho');
DEFINE('_KUNENA_DT_LMON_AUG', 'Agosto');
DEFINE('_KUNENA_DT_LMON_SEP', 'Setembro');
DEFINE('_KUNENA_DT_LMON_OCT', 'Outubro');
DEFINE('_KUNENA_DT_LMON_NOV', 'Novembro');
DEFINE('_KUNENA_DT_LMON_DEV', 'Dezembro');
DEFINE('_KUNENA_DT_MON_JAN', 'Jan');
DEFINE('_KUNENA_DT_MON_FEB', 'Feb');
DEFINE('_KUNENA_DT_MON_MAR', 'Mar');
DEFINE('_KUNENA_DT_MON_APR', 'Abr');
DEFINE('_KUNENA_DT_MON_MAY', 'Mai');
DEFINE('_KUNENA_DT_MON_JUN', 'Jun');
DEFINE('_KUNENA_DT_MON_JUL', 'Jul');
DEFINE('_KUNENA_DT_MON_AUG', 'Ago');
DEFINE('_KUNENA_DT_MON_SEP', 'Set');
DEFINE('_KUNENA_DT_MON_OCT', 'Out');
DEFINE('_KUNENA_DT_MON_NOV', 'Nov');
DEFINE('_KUNENA_DT_MON_DEV', 'Dez');
DEFINE('_KUNENA_CHILD_BOARD', 'Sub-Fórum (Child)');
DEFINE('_WHO_ONLINE_GUEST', 'Visitante');
DEFINE('_WHO_ONLINE_MEMBER', 'Membro');
DEFINE('_KUNENA_IMAGE_PROCESSOR_NONE', 'nenhum');
DEFINE('_KUNENA_IMAGE_PROCESSOR', 'Processador de Imagem:');
DEFINE('_KUNENA_INSTALL_CLICK_TO_CONTINUE', 'Clique aqui para continuar...');
DEFINE('_KUNENA_INSTALL_APPLY', 'Aplicar!');
DEFINE('_KUNENA_NO_ACCESS', 'Não tem acesso a este Fórum!');
DEFINE('_KUNENA_TIME_SINCE', '%time% atrás');
DEFINE('_KUNENA_DATE_YEARS', 'Anos');
DEFINE('_KUNENA_DATE_MONTHS', 'Meses');
DEFINE('_KUNENA_DATE_WEEKS','Semanas');
DEFINE('_KUNENA_DATE_DAYS', 'Dias');
DEFINE('_KUNENA_DATE_HOURS', 'Horas');
DEFINE('_KUNENA_DATE_MINUTES', 'Minutos');
// 1.0.2
DEFINE('_KUNENA_HEADERADD', 'Cabeçalho Fórum:');
DEFINE('_KUNENA_ADVANCEDDISPINFO', "Visualização Fórum");
DEFINE('_KUNENA_CLASS_SFX', "Sufixo da Classe CSS do Fórum");
DEFINE('_KUNENA_CLASS_SFXDESC', "Sufixo de CSS aplicado ao índice, mostrar categoria, visualização e permite diferentes aparências por fórum.");
DEFINE('_COM_A_USER_EDIT_TIME', 'Editar tempo do Utilizador');
DEFINE('_COM_A_USER_EDIT_TIME_DESC', 'Insira 0 para tempo ilimitado, ou então o espaço de tempo em segundos do post ou da última modificação para permitir edição.');
DEFINE('_COM_A_USER_EDIT_TIMEGRACE', 'Tempo de Graça da Edição do Utilizador');
DEFINE('_COM_A_USER_EDIT_TIMEGRACE_DESC', 'Padrão 600 [segundos], permite salvar a modificação até 600 segundos depois que o link de edição desaparece');
DEFINE('_KUNENA_HELPPAGE','Activar página de Ajuda');
DEFINE('_KUNENA_HELPPAGE_DESC','Se escolher &quot;Sim&quot;, um link no cabeçalho do menu do fórum será mostrado para a página de Ajuda');
DEFINE('_KUNENA_HELPPAGE_IN_FB','Mostrar ajuda no Kunena');
DEFINE('_KUNENA_HELPPAGE_IN_KUNENA_DESC','Se escolher &quot;Sim&quot; O texto do conteúdo de ajuda será incluído no Kunena e a ajuda externa não vai funcionar. <b>Nota:</b> Você pode adicionar um "ID de Conteúdo de Ajuda".');
DEFINE('_KUNENA_HELPPAGE_CID','ID de Conteúdo de Ajuda');
DEFINE('_KUNENA_HELPPAGE_CID_DESC','Deve escolher <b>"SIM"</b> na configuração "Mostrar ajuda no Kunena".');
DEFINE('_KUNENA_HELPPAGE_LINK','Ajuda em link de Página Externa');
DEFINE('_KUNENA_HELPPAGE_LINK_DESC','Se escolher um link externo para a Ajuda deve escolher <b>"NÃO"</b> na configuração "Mostrar ajuda no Kunena".');
DEFINE('_KUNENA_RULESPAGE','Activar página de Regras');
DEFINE('_KUNENA_RULESPAGE_DESC','Se escolher &quot;Sim&quot; um link será mostrado no menu do cabeçalho do fórum');
DEFINE('_KUNENA_RULESPAGE_IN_FB','Mostrar regras no Kunena');
DEFINE('_KUNENA_RULESPAGE_IN_KUNENA_DESC','Se escolher &quot;Sim&quot; o texto do conteúdo das regras será o do Kunena e o link externo não funcionará.<b>Nota:</b> Você pode adicionar um "ID de Conteúdo de Regras".');
DEFINE('_KUNENA_RULESPAGE_CID','ID de Conteúdo das Regras');
DEFINE('_KUNENA_RULESPAGE_CID_DESC','Deve escolher <b>"SIM"</b> na configuração "Mostrar regras no Kunena".');
DEFINE('_KUNENA_RULESPAGE_LINK','Link da página externa de Regras');
DEFINE('_KUNENA_RULESPAGE_LINK_DESC','Se escolher um link externo para as Regras deve seleccionar <b>"NÃO"</b> na configuração "Mostrar regras no Kunena".');
DEFINE('_KUNENA_AVATAR_GDIMAGE_NOT','Biblioteca GD não encontrada');
DEFINE('_KUNENA_AVATAR_GD2IMAGE_NOT','Biblioteca GD2 não encontrada');
DEFINE('_KUNENA_GD_INSTALLED','Biblioteca GD está disponível, versão&#32;');
DEFINE('_KUNENA_GD_NO_VERSION','Não é possível detectar a versão da biblioteca GD');
DEFINE('_KUNENA_GD_NOT_INSTALLED','Biblioteca GD não está instalada. Poderá obter mais informação em&#32;');
DEFINE('_KUNENA_AVATAR_SMALL_HEIGHT','Altura Imagem Pequena :');
DEFINE('_KUNENA_AVATAR_SMALL_WIDTH','Largura Imagem Pequena :');
DEFINE('_KUNENA_AVATAR_MEDIUM_HEIGHT','Altura Imagem Média :');
DEFINE('_KUNENA_AVATAR_MEDIUM_WIDTH','Largura Imagem Média :');
DEFINE('_KUNENA_AVATAR_LARGE_HEIGHT','Altura Imagem Grande :');
DEFINE('_KUNENA_AVATAR_LARGE_WIDTH','Largura Imagem Grande :');
DEFINE('_KUNENA_AVATAR_QUALITY','Qualidade Avatar');
DEFINE('_KUNENA_WELCOME','Bem vindo ao Kunena!');
DEFINE('_KUNENA_WELCOME_DESC','Obrigado por ter escolhido o Kunena como a sua solução de fórum. Neste écran pode ver as várias opções de gestão e uma apresentação resumida das estatísticas do Fórum. No lado esquerdo encontram-se os links para as mesmas opções de gestão que pode ver neste painel. Cada página tem instruções de como pode usar as respectivas ferramentas.');
DEFINE('_KUNENA_STATISTIC','Estatísticas');
DEFINE('_KUNENA_VALUE','Valor');
DEFINE('_GEN_CATEGORY','Categoria');
DEFINE('_GEN_STARTEDBY','Iniciado por:&#32;');
DEFINE('_GEN_STATS','Estatísticas');
DEFINE('_STATS_TITLE',' Estatísticas - Fórum');
DEFINE('_STATS_GEN_STATS','Estatísticas Gerais');
DEFINE('_STATS_TOTAL_MEMBERS','Membros:');
DEFINE('_STATS_TOTAL_REPLIES','Respostas:');
DEFINE('_STATS_TOTAL_TOPICS','Tópicos:');
DEFINE('_STATS_TODAY_TOPICS','Tópicos de Hoje:');
DEFINE('_STATS_TODAY_REPLIES','Respostas de Hoje:');
DEFINE('_STATS_TOTAL_CATEGORIES','Categorias:');
DEFINE('_STATS_TOTAL_SECTIONS','Secções:');
DEFINE('_STATS_LATEST_MEMBER','Último membro:');
DEFINE('_STATS_YESTERDAY_TOPICS','Tópicos de Ontem:');
DEFINE('_STATS_YESTERDAY_REPLIES','Respostas de Ontem:');
DEFINE('_STATS_POPULAR_PROFILE','10 Membros + Populares (Baseado nos Hits do Perfil)');
DEFINE('_STATS_TOP_POSTERS','Utilizadores que + publicam');
DEFINE('_STATS_POPULAR_TOPICS','Tópicos + Populares');
DEFINE('_COM_A_STATSPAGE','Activar Página de Estatísticas');
DEFINE('_COM_A_STATSPAGE_DESC','Se escolher &quot;Sim&quot;, será mostrado no cabeçalho do menu um link de acesso público para a página das estatísticas. Esta página irá mostrar diversas estatísticas acerca do fórum. <em>A página de estatísticas estará sempre disponível para o(s) Administrador(es).</em>');
DEFINE('_COM_C_JBSTATS','Estatísticas do Fórum');
DEFINE('_COM_C_JBSTATS_DESC','Estatísticas do Fórum');
define('_GEN_GENERAL','Geral');
define('_PERM_NO_READ','Não tem as permissões necessárias para aceder a este fórum.');
DEFINE ('_KUNENA_SMILEY_SAVED','Smile guardado.');
DEFINE ('_KUNENA_SMILEY_DELETED','Smile apagado.');
DEFINE ('_KUNENA_CODE_ALLREADY_EXITS','Código já existe.');
DEFINE ('_KUNENA_MISSING_PARAMETER','Parâmetro desconhecido/em falta');
DEFINE ('_KUNENA_RANK_ALLREADY_EXITS','Rank já existe.');
DEFINE ('_KUNENA_RANK_DELETED','Rank apagado.');
DEFINE ('_KUNENA_RANK_SAVED','Rank guardado.');
DEFINE ('_KUNENA_DELETE_SELECTED','Apagar Selecção');
DEFINE ('_KUNENA_MOVE_SELECTED','Mover Selecção');
DEFINE ('_KUNENA_REPORT_LOGGED','Logado');
DEFINE ('_KUNENA_GO','Ir');
DEFINE('_KUNENA_MAILFULL','Incluir o post completo no email a enviar aos subscritores.');
DEFINE('_KUNENA_MAILFULL_DESC','Se escolher &quot;Não&quot;, os subscritores só irão receber os títulos das novas mensagens.');
DEFINE('_KUNENA_HIDETEXT','Por favor faça login para ver este conteúdo!');
DEFINE('_BBCODE_HIDE','Texto escondido: [hide]qualquer texto a esconder[/hide] - esconder parte da mensagem aos Visitantes');
DEFINE('_KUNENA_FILEATTACH','Ficheiro anexado:&#32;');
DEFINE('_KUNENA_FILENAME','Nome ficheiro:&#32;');
DEFINE('_KUNENA_FILESIZE','Tamanho ficheiro:&#32;');
DEFINE('_KUNENA_MSG_CODE','Código:&#32;');
DEFINE('_KUNENA_CAPTCHA_ON','Sistema de protecção Spam');
DEFINE('_KUNENA_CAPTCHA_DESC','Sistema Antispam e antibot CAPTCHA On/Off');
DEFINE('_KUNENA_CAPDESC','Colocar o código aqui');
DEFINE('_KUNENA_CAPERR','Código incorrecto!');
DEFINE('_KUNENA_COM_A_REPORT', 'Reportar Mensagem');
DEFINE('_KUNENA_COM_A_REPORT_DESC', 'Se pretende autorizar que os utilizadores reportem qualquer mensagem seleccione &quot;Sim.&quot;');
DEFINE('_KUNENA_REPORT_MSG', 'Mensagem reportada');
DEFINE('_KUNENA_REPORT_REASON', 'Motivo');
DEFINE('_KUNENA_REPORT_MESSAGE', 'Sua mensagem');
DEFINE('_KUNENA_REPORT_SEND', 'Enviar Reporte');
DEFINE('_KUNENA_REPORT', 'Reportar ao moderador');
DEFINE('_KUNENA_REPORT_RSENDER', 'Reporte enviado por:&#32;');
DEFINE('_KUNENA_REPORT_RREASON', 'Motivo do Reporte:&#32;');
DEFINE('_KUNENA_REPORT_RMESSAGE', 'Mensagem de Reporte:&#32;');
DEFINE('_KUNENA_REPORT_POST_POSTER', 'Mensagem Postada por:&#32;');
DEFINE('_KUNENA_REPORT_POST_SUBJECT', 'Assunto da Mensagem:&#32;');
DEFINE('_KUNENA_REPORT_POST_MESSAGE', 'Mensagem:&#32;');
DEFINE('_KUNENA_REPORT_POST_LINK', 'Link da Mensagem:&#32;');
DEFINE('_KUNENA_REPORT_INTRO', 'foi-lhe enviada uma mensagem porque');
DEFINE('_KUNENA_REPORT_SUCCESS', 'Reporte enviado com sucesso!');
DEFINE('_KUNENA_EMOTICONS', 'Emoticons');
DEFINE('_KUNENA_EMOTICONS_SMILEY', 'Smile');
DEFINE('_KUNENA_EMOTICONS_CODE', 'Código');
DEFINE('_KUNENA_EMOTICONS_URL', 'URL');
DEFINE('_KUNENA_EMOTICONS_EDIT_SMILEY', 'Editar Smile');
DEFINE('_KUNENA_EMOTICONS_EDIT_SMILIES', 'Editar Smiles');
DEFINE('_KUNENA_EMOTICONS_EMOTICONBAR', 'EmoticonBar');
DEFINE('_KUNENA_EMOTICONS_NEW_SMILEY', 'Novo Smile');
DEFINE('_KUNENA_EMOTICONS_MORE_SMILIES', 'Mais Smiles');
DEFINE('_KUNENA_EMOTICONS_CLOSE_WINDOW', 'Fechar janela');
DEFINE('_KUNENA_EMOTICONS_ADDITIONAL_EMOTICONS', 'Adicionar Emoticons');
DEFINE('_KUNENA_EMOTICONS_PICK_A_SMILEY', 'Escolha um smile');
DEFINE('_KUNENA_MAMBOT_SUPPORT', 'Suporte a Joomla Plugin (Mambot)');
DEFINE('_KUNENA_MAMBOT_SUPPORT_DESC', 'Permitir Plugin (Mambot) do Joomla');
DEFINE('_KUNENA_MYPROFILE_PLUGIN_SETTINGS', 'Configurações do plugin Meu Perfil');
DEFINE('_KUNENA_USERNAMECANCHANGE', 'Permitir alteração nome de utilizador');
DEFINE('_KUNENA_USERNAMECANCHANGE_DESC', 'Permitir a alteração do nome de utilizador na página do plugin Meu Perfil');
DEFINE ('_KUNENA_RECOUNTFORUMS','Reiniciar estatísticas Categorias');
DEFINE ('_KUNENA_RECOUNTFORUMS_DONE','Todas as estatísticas das categorias foram reiniciadas.');
DEFINE ('_KUNENA_EDITING_REASON','Motivo para a edição');
DEFINE ('_KUNENA_EDITING_LASTEDIT','Última edição');
DEFINE ('_KUNENA_BY','Por');
DEFINE ('_KUNENA_REASON','Motivo');
DEFINE('_GEN_GOTOBOTTOM', 'Para baixo');
DEFINE('_GEN_GOTOTOP', 'Para cima');
DEFINE('_STAT_USER_INFO', 'Informação utilizador');
DEFINE('_USER_SHOWEMAIL', 'Mostrar E-mail'); // <=FB 1.0.3
DEFINE('_USER_SHOWONLINE', 'Mostrar Online');
DEFINE('_KUNENA_HIDDEN_USERS', 'Ocultar utilizadores');
DEFINE('_KUNENA_SAVE', 'Guardar');
DEFINE('_KUNENA_RESET', 'Limpar');
DEFINE('_KUNENA_DEFAULT_GALLERY', 'Galeria Padrão');
DEFINE('_KUNENA_MYPROFILE_PERSONAL_INFO', 'Informação pessoal');
DEFINE('_KUNENA_MYPROFILE_SUMMARY', 'Sumário');
DEFINE('_KUNENA_MYPROFILE_MYAVATAR', 'Meu Avatar');
DEFINE('_KUNENA_MYPROFILE_FORUM_SETTINGS', 'Configurações do Fórum');
DEFINE('_KUNENA_MYPROFILE_LOOK_AND_LAYOUT', 'Aspecto e Estilo');
DEFINE('_KUNENA_MYPROFILE_MY_PROFILE_INFO', 'Informação Meu Perfil');
DEFINE('_KUNENA_MYPROFILE_MY_POSTS', 'Meus Posts');
DEFINE('_KUNENA_MYPROFILE_MY_SUBSCRIBES', 'Minhas subscrições');
DEFINE('_KUNENA_MYPROFILE_MY_FAVORITES', 'Meus Favoritos');
DEFINE('_KUNENA_MYPROFILE_PRIVATE_MESSAGING', 'Sistema de Mensagens Privadas');
DEFINE('_KUNENA_MYPROFILE_INBOX', 'A Receber');
DEFINE('_KUNENA_MYPROFILE_NEW_MESSAGE', 'Nova Mensagem');
DEFINE('_KUNENA_MYPROFILE_OUTBOX', 'Itens Enviados');
DEFINE('_KUNENA_MYPROFILE_TRASH', 'Lixeira');
DEFINE('_KUNENA_MYPROFILE_SETTINGS', 'Configurações');
DEFINE('_KUNENA_MYPROFILE_CONTACTS', 'Contactos');
DEFINE('_KUNENA_MYPROFILE_BLOCKEDLIST', 'Lista de Bloqueados');
DEFINE('_KUNENA_MYPROFILE_ADDITIONAL_INFO', 'Informação Adicional');
DEFINE('_KUNENA_MYPROFILE_NAME', 'Nome');
DEFINE('_KUNENA_MYPROFILE_USERNAME', 'Nome utilizador');
DEFINE('_KUNENA_MYPROFILE_EMAIL', 'E-mail');
DEFINE('_KUNENA_MYPROFILE_USERTYPE', 'Tipo de utilizador');
DEFINE('_KUNENA_MYPROFILE_REGISTERDATE', 'Data de Registo');
DEFINE('_KUNENA_MYPROFILE_LASTVISITDATE', 'Data da última visita');
DEFINE('_KUNENA_MYPROFILE_POSTS', 'Posts');
DEFINE('_KUNENA_MYPROFILE_PROFILEVIEW', 'Ver Perfil');
DEFINE('_KUNENA_MYPROFILE_PERSONALTEXT', 'Texto pessoal');
DEFINE('_KUNENA_MYPROFILE_GENDER', 'Gênero');
DEFINE('_KUNENA_MYPROFILE_BIRTHDATE', 'Data aniversário');
DEFINE('_KUNENA_MYPROFILE_BIRTHDATE_DESC', 'Ano (YYYY) - Mês (MM) - Dia (DD)');
DEFINE('_KUNENA_MYPROFILE_LOCATION', 'Localização');
DEFINE('_KUNENA_MYPROFILE_ICQ', 'ICQ');
DEFINE('_KUNENA_MYPROFILE_ICQ_DESC', 'O seu número ICQ.');
DEFINE('_KUNENA_MYPROFILE_AIM', 'AIM');
DEFINE('_KUNENA_MYPROFILE_AIM_DESC', 'O seu nickname AOL Instant Messenger.');
DEFINE('_KUNENA_MYPROFILE_YIM', 'YIM');
DEFINE('_KUNENA_MYPROFILE_YIM_DESC', 'O seu nickname Yahoo! Instant Messenger.');
DEFINE('_KUNENA_MYPROFILE_SKYPE', 'SKYPE');
DEFINE('_KUNENA_MYPROFILE_SKYPE_DESC', 'O seu nickname Skype.');
DEFINE('_KUNENA_MYPROFILE_GTALK', 'GTALK');
DEFINE('_KUNENA_MYPROFILE_GTALK_DESC', 'O seu nickname Gtalk.');
DEFINE('_KUNENA_MYPROFILE_WEBSITE', 'Site Web');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_NAME', 'O nome do Site Web');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_NAME_DESC', 'Exemplo: Kunena!');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_URL', 'URL Site Web');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_URL_DESC', 'Exemplo: www.Kunena.com');
DEFINE('_KUNENA_MYPROFILE_MSN', 'MSN');
DEFINE('_KUNENA_MYPROFILE_MSN_DESC', 'O seu endereço de email MSN Messenger.');
DEFINE('_KUNENA_MYPROFILE_SIGNATURE', 'Assinatura');
DEFINE('_KUNENA_MYPROFILE_MALE', 'Masculino');
DEFINE('_KUNENA_MYPROFILE_FEMALE', 'Feminino');
DEFINE('_KUNENA_BULKMSG_DELETED', 'O(s) Post(s) foi(ram) apagado(s) com sucesso');
DEFINE('_KUNENA_DATE_YEAR', 'Ano');
DEFINE('_KUNENA_DATE_MONTH', 'Mês');
DEFINE('_KUNENA_DATE_WEEK','Semana');
DEFINE('_KUNENA_DATE_DAY', 'Dia');
DEFINE('_KUNENA_DATE_HOUR', 'Hora');
DEFINE('_KUNENA_DATE_MINUTE', 'Minuto');
DEFINE('_KUNENA_IN_FORUM', '&#32;no Fórum:&#32;');
DEFINE('_KUNENA_FORUM_AT', '&#32;Fórum em:&#32;');
DEFINE('_KUNENA_QMESSAGE_NOTE', 'NOTA: Ainda que não sejam mostrados no painel os botões do código e do smile, continuam a poder ser utilizados.');

// 1.0.1
DEFINE ('_KUNENA_FORUMTOOLS','Ferramentas do Fórum');

//userlist
DEFINE ('_KUNENA_USRL_USERLIST','Lista de utilizadores');
DEFINE ('_KUNENA_USRL_REGISTERED_USERS','%s tem <b>%d</b> utilizadores registados');
DEFINE ('_KUNENA_USRL_SEARCH_ALERT','Coloque um valor para a procura!');
DEFINE ('_KUNENA_USRL_SEARCH','Procurar utilizador');
DEFINE ('_KUNENA_USRL_SEARCH_BUTTON','Procurar');
DEFINE ('_KUNENA_USRL_LIST_ALL','Listar todos');
DEFINE ('_KUNENA_USRL_NAME','Nome');
DEFINE ('_KUNENA_USRL_USERNAME','Nome utilizador');
DEFINE ('_KUNENA_USRL_GROUP','Grupo');
DEFINE ('_KUNENA_USRL_POSTS','Posts');
DEFINE ('_KUNENA_USRL_KARMA','Popularidade');
DEFINE ('_KUNENA_USRL_HITS','Hits');
DEFINE ('_KUNENA_USRL_EMAIL','E-mail');
DEFINE ('_KUNENA_USRL_USERTYPE','Tipo de utilizador');
DEFINE ('_KUNENA_USRL_JOIN_DATE','Data de registo');
DEFINE ('_KUNENA_USRL_LAST_LOGIN','Último login');
DEFINE ('_KUNENA_USRL_NEVER','Nunca');
DEFINE ('_KUNENA_USRL_ONLINE','Estado');
DEFINE ('_KUNENA_USRL_AVATAR','Imagem');
DEFINE ('_KUNENA_USRL_ASC','Ascendente');
DEFINE ('_KUNENA_USRL_DESC','Descendente');
DEFINE ('_KUNENA_USRL_DISPLAY_NR','Mostrar');
DEFINE ('_KUNENA_USRL_DATE_FORMAT','%d/%m/%Y');

DEFINE('_KUNENA_ADMIN_CONFIG_PLUGINS','Plugins');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST','Lista de utilizadores');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_ROWS_DESC','Número de linhas da lista de utilizadores');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_ROWS','Número de linhas da lista de utilizadores');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERONLINE','Estado Online');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERONLINE_DESC','Mostrar o estado dos utilizadores online');

DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_AVATAR','Mostrar Avatar');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERLIST_AVATAR_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_NAME','Mostrar nome real');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_name_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERNAME','Mostrar nome de utilizador');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERNAME_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_GROUP','Mostrar grupo de utilizador');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_GROUP_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_POSTS','Mostrar número de Posts');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_POSTS_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_KARMA','Mostrar Popularidade');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_KARMA_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_EMAIL','Mostrar E-mail');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_EMAIL_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERTYPE','Mostrar tipo de utilizador');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERTYPE_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_JOINDATE','Mostrar data de registo');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_JOINDATE_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_LASTVISITDATE','Mostrar data da última visita');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_LASTVISITDATE_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_HITS','Mostrar Hits do Perfil');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_HITS_DESC','');
DEFINE('_KUNENA_DBWIZ', 'Wizard Base de Dados');
DEFINE('_KUNENA_DBMETHOD', 'Escolha qual o método que pretende para efectuar a conclusão da instalação:');
DEFINE('_KUNENA_DBCLEAN', 'Limpar a instalação');
DEFINE('_KUNENA_DBUPGRADE', 'Actualizar do Joomlaboard');
DEFINE('_KUNENA_TOPLEVEL', 'Nivel Superior da Categoria');
DEFINE('_KUNENA_REGISTERED', 'Registado');
DEFINE('_KUNENA_PUBLICBACKEND', 'Backend Público');
DEFINE('_KUNENA_SELECTANITEMTO', 'Seleccione um item para');
DEFINE('_KUNENA_ERRORSUBS', 'Ocorreu um erro ao apagar as mensagens e subscrições.');
DEFINE('_KUNENA_WARNING', 'Aviso...');
DEFINE('_KUNENA_CHMOD1', 'Precisa aplicar CHMOD 766 para que o arquivo seja actualizado.');
DEFINE('_KUNENA_YOURCONFIGFILEIS', 'O seu ficheiro de configuração é');
DEFINE('_KUNENA_KUNENA', 'Kunena');
DEFINE('_KUNENA_CLEXUS', 'Clexus PM');
DEFINE('_KUNENA_CB', 'Community Builder');
DEFINE('_KUNENA_MYPMS', 'myPMS II Open Source');
DEFINE('_KUNENA_UDDEIM', 'Uddeim');
DEFINE('_KUNENA_JIM', 'JIM');
DEFINE('_KUNENA_MISSUS', 'Missus');
DEFINE('_KUNENA_SELECTTEMPLATE', 'Seleccione Template');
DEFINE('_KUNENA_CONFIGSAVED', 'Configuração guardada.');
DEFINE('_KUNENA_CONFIGNOTSAVED', 'ERRO FATAL: Não foi possível guardar a configuração.');
DEFINE('_KUNENA_TFINW', 'O ficheiro não é editável.');
DEFINE('_KUNENA_FBCFS', 'Fiheiro CSS do Kunena guardado.');
DEFINE('_KUNENA_SELECTMODTO', 'Escolha um moderador para');
DEFINE('_KUNENA_CHOOSEFORUMTOPRUNE', 'Deverá escolher um fórum para optimizar!');
DEFINE('_KUNENA_DELMSGERROR', 'Falha ao apagar mensagens:');
DEFINE('_KUNENA_DELMSGERROR1', 'Falha ao apagar o texto das mensagens:');
DEFINE('_KUNENA_CLEARSUBSFAIL', 'Falha ao limpar as Subscrições:');
DEFINE('_KUNENA_FORUMPRUNEDFOR', 'Fórum optimizado para');
DEFINE('_KUNENA_PRUNEDAYS', 'dias');
DEFINE('_KUNENA_PRUNEDELETED', 'Apagado:');
DEFINE('_KUNENA_PRUNETHREADS', 'Tópicos');
DEFINE('_KUNENA_ERRORPRUNEUSERS', 'Erro a optimizar utilizadores:');
DEFINE('_KUNENA_USERSPRUNEDDELETED', 'Utilizadores optimizados. Apagados:'); // <=FB 1.0.3
DEFINE('_KUNENA_PRUNEUSERPROFILES', 'Perfis de utilizador'); // <=FB 1.0.3
DEFINE('_KUNENA_NOPROFILESFORPRUNNING', 'Não foram encontrados perfis elegíveis para optimizar.'); // <=FB 1.0.3
DEFINE('_KUNENA_TABLESUPGRADED', 'Tabelas Kunena foram actualizadas para a versão');
DEFINE('_KUNENA_FORUMCATEGORY', 'Categoria do Fórum');
DEFINE('_KUNENA_IMGDELETED', 'Imagem apagada');
DEFINE('_KUNENA_FILEDELETED', 'Ficheiro apagado');
DEFINE('_KUNENA_NOPARENT', 'Não existe Parente');
DEFINE('_KUNENA_DIRCOPERR', 'Erro: Ficheiro');
DEFINE('_KUNENA_DIRCOPERR1', 'não pode ser copiado!\n');
DEFINE('_KUNENA_INSTALL1', '<strong>Fórum Kunena</strong> Componente <em>para o Joomla! </em> <br />&copy; 2008 - 2009 por www.Kunena.com<br />Todos os direitos reservados.');
DEFINE('_KUNENA_INSTALL2', 'Transferência/Instalação :</code></strong><br /><br /><font color="red"><b>efectuada com sucesso');
DEFINE('_KUNENA_FORUMPRF_TITLE', 'Configurações do Perfil');
DEFINE('_KUNENA_FORUMPRF', 'Perfil');
DEFINE('_KUNENA_FORUMPRRDESC', 'Se tiver instalado o Community Builder ou JomSocial, pode configurar o Kunena para utilizar os seus perfis de utilizadores.');
DEFINE('_KUNENA_USERPROFILE_PROFILE', 'Perfil');
DEFINE('_KUNENA_USERPROFILE_PROFILEHITS', '<b>Visualizações do Perfil</b>');
DEFINE('_KUNENA_USERPROFILE_MESSAGES', 'Todas as mensagens do Fórum');
DEFINE('_KUNENA_USERPROFILE_TOPICS', 'Tópicos');
DEFINE('_KUNENA_USERPROFILE_STARTBY', 'Iniciado por');
DEFINE('_KUNENA_USERPROFILE_CATEGORIES', 'Categorias');
DEFINE('_KUNENA_USERPROFILE_DATE', 'Data');
DEFINE('_KUNENA_USERPROFILE_HITS', 'Hits');
DEFINE('_KUNENA_USERPROFILE_NOFORUMPOSTS', 'Não existem Posts no Fórum');
DEFINE('_KUNENA_TOTALFAVORITE', 'Preferido por: &#32;');
DEFINE('_KUNENA_SHOW_CHILD_CATEGORY_COLON', 'Número de colunas no Sub-Fórum (Child) &#32;');
DEFINE('_KUNENA_SHOW_CHILD_CATEGORY_COLONDESC', 'Número de colunas dos sub-fóruns abaixo da categoria principal&#32;');
DEFINE('_KUNENA_SUBSCRIPTIONSCHECKED', 'Caixa de subscrição de Post por defeito?');
DEFINE('_KUNENA_SUBSCRIPTIONSCHECKED_DESC', 'Escolha &quot;Sim&quot; Se quer que a caixa de subscrição esteja sempre marcada');
// Errors (Re-integration from Joomlaboard 1.2)
DEFINE('_KUNENA_ERROR1', 'Categoria / O Fórum deve ter um nome');
// Forum Configuration (New in Kunena)
DEFINE('_KUNENA_SHOWSTATS', 'Mostrar Estatísticas');
DEFINE('_KUNENA_SHOWSTATSDESC', 'Se pretende mostrar as estatísticas seleccione &quot;Sim.&quot;');
DEFINE('_KUNENA_SHOWWHOIS', 'Mostrar quem está Online');
DEFINE('_KUNENA_SHOWWHOISDESC', 'Se pretende mostrar quem está online, seleccione &quot;Sim.&quot;');
DEFINE('_KUNENA_STATSGENERAL', 'Mostrar estatísticas gerais');
DEFINE('_KUNENA_STATSGENERALDESC', 'Se pretende mostrar as estatísticas gerais, seleccione &quot;Sim.&quot;');
DEFINE('_KUNENA_USERSTATS', 'Mostrar estatísticas dos utilizadores mais populares');
DEFINE('_KUNENA_USERSTATSDESC', 'Se pretende mostrar as estatísticas dos utilizadores mais populares, seleccione &quot;Sim.&quot;');
DEFINE('_KUNENA_USERNUM', 'Número de utilizadores populares');
DEFINE('_KUNENA_USERPOPULAR', 'Mostrar estatísticas dos tópicos mais populares');
DEFINE('_KUNENA_USERPOPULARDESC', 'Se pretende mostrar as estatísticas dos tópicos mais populares, seleccione &quot;Sim.&quot;');
DEFINE('_KUNENA_NUMPOP', 'Número de tópicos mais populares');
DEFINE('_KUNENA_INFORMATION',
    'The Kunena team is proud to announce the release of Kunena 1.0.8. It is a powerful and stylish forum component for a well deserved content management system, Joomla!. It is initially based on the hard work of Joomlaboard and Fireboard and most of our praises goes to their team. Some of the main features of Kunena can be listed as below (in addition to JB&#39;s current features):<br /><br /><ul><li>A much more designer friendly forum system. It is close to SMF templating system having a simpler structue. With very few steps you can modify the total look of the forum. Thanks goes to the great designers in our team.</li><li>Unlimited subcategory system with better administration backend.</li><li>Faster system and better coding experience for 3rd parties.</li><li>The same<br /></li><li>Profilebox at the top of the forum</li><li>Support for popular PM systems, such as ClexusPM and Uddeim</li><li>Basic plugin system (practical rather than perfec)</li><li>Language-defined icon system.<br /></li><li>Sharing image system of other templates. So, choice between templates and image series is possible</li><li>You can add Joomla modules inside the forum template itself. Wanted to have banner inside your forum?</li><li>Favourite threads selection and management</li><li>Forum spotlights and highlights</li><li>Forum announcements and its panel</li><li>Latest messages (Tabbed)</li><li>Statistics at bottom</li><li>Who&#39;s online, on what page?</li><li>Category specific image system</li><li>Enhanced pathway</li><li>RSS, PDF output</li><li>Advanced search (under developement)</li><li>Community Builder and JomSocial profile options</li><li>Avatar management : Community Builder and JomSocial options<br /></li></ul><br />This is a collaborative work of several developers and designers that have kindly participated and made this release come true. Here we thank all of them and wish that you enjoy this release!<br /><br />Kunena! team<br /></td></tr></table>');
DEFINE('_KUNENA_INSTRUCTIONS', 'Instruções');
DEFINE('_KUNENA_FINFO', 'Informação do Kunena Fórum');
DEFINE('_KUNENA_CSSEDITOR', 'Editor do CSS da Template do Kunena');
DEFINE('_KUNENA_PATH', 'Caminho:');
DEFINE('_KUNENA_CSSERROR', 'NOTA:O ficheiro CSS da template deve ser editável para poder guardar as alterações.');
// User Management
DEFINE('_KUNENA_FUM', 'Gerir o Perfil do Utilizador Kunena');
DEFINE('_KUNENA_SORTID', 'ordenar por ID');
DEFINE('_KUNENA_SORTMOD', 'ordenar por moderador');
DEFINE('_KUNENA_SORTNAME', 'ordenar por nome');
DEFINE('_KUNENA_VIEW', 'Ver');
DEFINE('_KUNENA_NOUSERSFOUND', 'Não foram encontrados perfis de utilizador.');
DEFINE('_KUNENA_ADDMOD', 'Adicionar moderador para');
DEFINE('_KUNENA_NOMODSAV', 'Não foram encontrados possíveis moderadores. Ler a \'nota\' em baixo.');
DEFINE('_KUNENA_NOTEUS',
    'NOTA: Somente os Utilizadores que têm o nível de moderação em seu perfil no Kunena serão aqui mostrados. Para ser capaz de adicionar um moderador dê a um Utilizador o nível de moderador, vá a <a href="index2.php?option=com_Kunena&task=profiles">Gerir Utilizadores</a> e procure pelo Utilizador que você quer tornar um moderador. Depois Seleccione seu perfil e actualize-o. O nível de moderador só pode ser atribuído por um administrador do site.');
DEFINE('_KUNENA_PROFFOR', 'Perfil para');
DEFINE('_KUNENA_GENPROF', 'Opções gerais do Perfil');
//DEFINE('_KUNENA_PREFVIEW', 'Prefered Viewtype:');
DEFINE('_KUNENA_PREFOR', 'Ordem preferida das mensagens:');
DEFINE('_KUNENA_ISMOD', 'O seu Moderador:');
DEFINE('_KUNENA_ISADM', '<strong>Sim</strong> (não editável, este utilizador é um (super)administrador do site)');
DEFINE('_KUNENA_COLOR', 'Cor');
DEFINE('_KUNENA_UAVATAR', 'Usar avatar:');
DEFINE('_KUNENA_NS', 'Nada seleccionado');
DEFINE('_KUNENA_DELSIG', '&#32;seleccione esta caixa para apagar esta assinatura');
DEFINE('_KUNENA_DELAV', '&#32;seleccione esta caixa para apagar este avatar');
DEFINE('_KUNENA_SUBFOR', 'Subscrições para');
DEFINE('_KUNENA_NOSUBS', 'Não foram encontradas subscrições para este utilizador');
// Forum Administration (Re-integration from Joomlaboard 1.2)
DEFINE('_KUNENA_BASICS', 'Básica');
DEFINE('_KUNENA_BASICSFORUM', 'Informação básica acerca do Fórum');
DEFINE('_KUNENA_PARENT', 'Parente:');
DEFINE('_KUNENA_PARENTDESC',
    'NOTA: Para criar uma Categoria, escolha \'Categoria de Nível Superior\' como parente. A Categoria serve como contentor dos Fóruns.<br />Um Fórum<strong>somente</strong> pode ser criado numa categoria préviamente criada. <br />As mensagens <strong>NÃO</strong>podem ser publicadas em Categorias, sómente em sub-fóruns (Child).');
DEFINE('_KUNENA_BASICSFORUMINFO', 'Nome do Fórum e descrição');
DEFINE('_KUNENA_NAMEADD', 'Nome:');
DEFINE('_KUNENA_DESCRIPTIONADD', 'Descrição:');
DEFINE('_KUNENA_ADVANCEDDESC', 'Configuração avançada do Fórum');
DEFINE('_KUNENA_ADVANCEDDESCINFO', 'Segurança do Fórum e Acesso');
DEFINE('_KUNENA_LOCKEDDESC', 'Escolha &quot;Sim&quot; se pretende fechar este fórum. Ninguém, excepto Moderadores e Administradores, podem criar novos tópicos e/ou responder a um fórum bloqueado (ou mover posts).');
DEFINE('_KUNENA_LOCKED1', 'Fechado:');
DEFINE('_KUNENA_PUBACC', 'Nível Acesso Público:');
DEFINE('_KUNENA_PUBACCDESC',
    'Para criar um fórum privado deve especificar aqui o nível mínimo de acesso que pode visualizar/entrar no fórum. Por padrão o nível de acesso minimo é &quot;Todos&quot;.<br /><br /><b>NOTA</b>:Se restringir o acesso numa Categoria inteira para um ou mais grupos, ele esconderá todos os Fóruns que ela contém a qualquer pessoa que não tennha os privilégios apropriados na Categoria <b>mesmo</b> se um ou mais destes Fóruns têm um nível de acesso mais baixo configurado! Isto também se aplica a Moderadores; você terá que acrescentar um Moderador à lista de moderadores da Categoria se ele(s) não tiver o nível de grupo apropriado para ver a Categoria.<br /> Isto é independente do facto de que Categorias não podem ser moderadas; Ainda assim moderadores podem ser acrescentados à lista de moderadores.<br /><br /><br />');
DEFINE('_KUNENA_CGROUPS', 'Incluir Sub-Grupos:');
DEFINE('_KUNENA_CGROUPSDESC', 'Os Sub-Grupos devem ter permissão de acesso também? Se configurar para &quot;Não&quot; o acesso a este fórum será restrito <b>somente</b> ao grupo selecionado<br /><br />');
DEFINE('_KUNENA_ADMINLEVEL', 'Nível Acesso Administração:');
DEFINE('_KUNENA_ADMINLEVELDESC',
    'Se criar um fórum com restrições ao Acesso Público, pode especificar aqui um Nível de Acesso de Administração adicional.<br /> Se restringir o acesso ao fórum para um grupo especial de Utilizadores do Frontend e não especificar um Grupo de Backend Público aqui, os administradores não serão capazes de entrar e ver o Fórum.');
DEFINE('_KUNENA_ADVANCED', 'Avançado');
DEFINE('_KUNENA_CGROUPS1', 'Incluir Sub-Grupos:');
DEFINE('_KUNENA_CGROUPS1DESC', 'Os Sub-Grupos devem ter permissão de acesso também? Se configurar para &quot;Não&quot; o acesso a este fórum será restrito <b>somente</b> ao grupo seleccionado<br /><br />');
DEFINE('_KUNENA_REV', 'Rever posts:');
DEFINE('_KUNENA_REVDESC',
    'Escolha &quot;Sim&quot; se quer que os posts sejam revistos pelos Moderadores antes de serem publicados neste fórum. Isto só é útil num fórum Moderado!!<br />Se configurar isto sem qualquer Moderador especificado, o Admininistrador do Site é o único responsável por aprovar/apagar os posts inseridos que serão mantidos em \'espera\'!<br /><br />');
DEFINE('_KUNENA_MOD_NEW', 'Moderação');
DEFINE('_KUNENA_MODNEWDESC', 'Moderação do Fórum e seus Moderadores');
DEFINE('_KUNENA_MOD', 'Moderado:');
DEFINE('_KUNENA_MODDESC',
    'Escolha &quot;Sim&quot; se quiser poder nomear Moderadores para este fórum.<br /><strong>Nota:</strong> Isto não significa que novos posts possam ser revistos antes da publicação no fórum! Você precisará seleccionar a opção de Revisão no menu Avançado.<br /><br /> <strong>NOTA:</strong> Depois de ajustar Moderação para &quot;Sim&quot; tem que gravar a configuração do fórum em primeiro lugar antes de poder adicionar Moderadores.<br /><br />');
DEFINE('_KUNENA_MODHEADER', 'Configurações de moderação para este fórum');
DEFINE('_KUNENA_MODSASSIGNED', 'Moderador(es) autorizado(s) para este fórum:');
DEFINE('_KUNENA_NOMODS', 'Não existe(m) Moderador(es) autorizado(s) para este fórum');
// Some General Strings (Improvement in Kunena)
DEFINE('_KUNENA_EDIT', 'Editar');
DEFINE('_KUNENA_ADD', 'Adicionar');
// Reorder (Re-integration from Joomlaboard 1.2)
DEFINE('_KUNENA_MOVEUP', 'Para cima');
DEFINE('_KUNENA_MOVEDOWN', 'Para baixo');
// Groups - Integration in Kunena
DEFINE('_KUNENA_ALLREGISTERED', 'Os registados');
DEFINE('_KUNENA_EVERYBODY', 'Todos');
// Removal of hardcoded strings in admin panel (Re-integration from Joomlaboard 1.2)
DEFINE('_KUNENA_REORDER', 'Reordenar');
DEFINE('_KUNENA_CHECKEDOUT', 'Retirados');
DEFINE('_KUNENA_ADMINACCESS', 'Acesso Administração');
DEFINE('_KUNENA_PUBLICACCESS', 'Acesso Público');
DEFINE('_KUNENA_PUBLISHED', 'Publicado');
DEFINE('_KUNENA_REVIEW', 'Revisto');
DEFINE('_KUNENA_MODERATED', 'Moderado');
DEFINE('_KUNENA_LOCKED', 'Fechado');
DEFINE('_KUNENA_CATFOR', 'Categoria / Fórum');
DEFINE('_KUNENA_ADMIN', 'Administração Kunena');
DEFINE('_KUNENA_CP', 'Painel de Controle Kunena');
// Configuration page - Headings (Re-integrated from Joomlaboard 1.2)
DEFINE('_COM_A_AVATAR_INTEGRATION', 'Integração Avatar');
DEFINE('_COM_A_RANKS_SETTINGS', 'Ranks');
DEFINE('_COM_A_RANKING_SETTINGS', 'Configurações do Ranking');
DEFINE('_COM_A_AVATAR_SETTINGS', 'Configurações do Avatar');
DEFINE('_COM_A_SECURITY_SETTINGS', 'Configurações de Segurança');
DEFINE('_COM_A_BASIC_SETTINGS', 'Configurações Básicas');
// Kunena 1.0.0
//
DEFINE('_COM_A_FAVORITES', 'Permitir Favoritos');
DEFINE('_COM_A_FAVORITES_DESC', 'Escolha &quot;Sim&quot; se quiser que os Utilizadores registados adicionem tópicos aos favoritos&#32;');
DEFINE('_USER_UNFAVORITE_ALL', 'Marque esta caixa se pretende <b><u>remover como favorito</u></b> todos os tópicos (incluindo os não visíveis que tiveram problemas)');
DEFINE('_VIEW_FAVORITETXT', 'Adicionar este tópico como favorito&#32;');
DEFINE('_USER_UNFAVORITE_YES', 'Retirar este tópico dos favoritos.');
DEFINE('_POST_FAVORITED_TOPIC', 'Este tópico foi adicionado aos seus favoritos.');
DEFINE('_VIEW_UNFAVORITETXT', 'Retirar favorito');
DEFINE('_VIEW_UNSUBSCRIBETXT', 'Retirar subscrição');
DEFINE('_USER_NOFAVORITES', 'Nenhum Favorito');
DEFINE('_POST_SUCCESS_FAVORITE', 'O seu pedido para adicionar aos favoritos está a ser processado.');
DEFINE('_COM_A_MESSAGES_SEARCH', 'Procurar Resultados');
DEFINE('_COM_A_MESSAGES_DESC_SEARCH', 'Mensagens por página do resultado da procura');
DEFINE('_KUNENA_USE_JOOMLA_STYLE', 'Usar estilo Joomla?');
DEFINE('_KUNENA_USE_JOOMLA_STYLE_DESC', 'Se pretende usar o estilo Joomla, escolha &quot;Sim&quot;. (class: like sectionheader, sectionentry1 ...)&#32;');
DEFINE('_KUNENA_SHOW_CHILD_CATEGORY_ON_LIST', 'Mostrar Imagem Sub-Categoria');
DEFINE('_KUNENA_SHOW_CHILD_CATEGORY_ON_LIST_DESC', 'Se quiser mostrar um pequeno ícone da sub-categoria na sua lista do fórum, escolha &quot;Sim&quot;.&#32;');
DEFINE('_KUNENA_SHOW_ANNOUNCEMENT', 'Mostrar Comunicados');
DEFINE('_KUNENA_SHOW_ANNOUNCEMENT_DESC', 'Escolha &quot;Sim&quot;, se quiser mostrar a caixa de anúncio/comunicado na página do fórum.');
DEFINE('_KUNENA_SHOW_AVATAR_ON_CAT', 'Mostrar Avatar Lista Categorias?');
DEFINE('_KUNENA_SHOW_AVATAR_ON_CAT_DESC', 'Escolha &quot;Sim&quot;, se quiser mostrar o avatar na lista de categoria de fórum.');
DEFINE('_KUNENA_RECENT_POSTS', 'Configurações dos Posts Recentes');
DEFINE('_KUNENA_SHOW_LATEST_MESSAGES', 'Mostrar Posts Recentes');
DEFINE('_KUNENA_SHOW_LATEST_MESSAGES_DESC', 'Escolha &quot;Sim&quot; se quiser mostrar o plugin de posts recentes em seu fórum');
DEFINE('_KUNENA_NUMBER_OF_LATEST_MESSAGES', 'Número de Posts Recentes');
DEFINE('_KUNENA_NUMBER_OF_LATEST_MESSAGES_DESC', 'Número de Posts Recentes');
DEFINE('_KUNENA_COUNT_PER_PAGE_LATEST_MESSAGES', 'Limite de Posts por tab&#32;');
DEFINE('_KUNENA_COUNT_PER_PAGE_LATEST_MESSAGES_DESC', 'Número de Posts por cada tab');
DEFINE('_KUNENA_LATEST_CATEGORY', 'Mostrar Categoria');
DEFINE('_KUNENA_LATEST_CATEGORY_DESC', 'Especificar a categoria que pretende mostrar nos Posts recentes. Por exemplo: 2,3,7&#32;');
DEFINE('_KUNENA_SHOW_LATEST_SINGLE_SUBJECT', 'Mostrar Assunto');
DEFINE('_KUNENA_SHOW_LATEST_SINGLE_SUBJECT_DESC', 'Mostrar Assunto');
DEFINE('_KUNENA_SHOW_LATEST_REPLY_SUBJECT', 'Mostrar Assunto Respostas');
DEFINE('_KUNENA_SHOW_LATEST_REPLY_SUBJECT_DESC', 'Mostrar Assunto Respostas (Re:)');
DEFINE('_KUNENA_LATEST_SUBJECT_LENGTH', 'Tamanho do assunto');
DEFINE('_KUNENA_LATEST_SUBJECT_LENGTH_DESC', 'Tamanho do assunto');
DEFINE('_KUNENA_SHOW_LATEST_DATE', 'Mostrar Data');
DEFINE('_KUNENA_SHOW_LATEST_DATE_DESC', 'Mostrar Data');
DEFINE('_KUNENA_SHOW_LATEST_HITS', 'Mostrar Hits');
DEFINE('_KUNENA_SHOW_LATEST_HITS_DESC', 'Mostrar Hits');
DEFINE('_KUNENA_SHOW_AUTHOR', 'Mostrar Autor');
DEFINE('_KUNENA_SHOW_AUTHOR_DESC', '1=nome de utilizador, 2=nome, 0=nenhum');
DEFINE('_KUNENA_STATS', 'Configurações do Plugin Estatísticas&#32;');
DEFINE('_KUNENA_CATIMAGEPATH', 'Caminho (path) das Imagens da Categoria&#32;');
DEFINE('_KUNENA_CATIMAGEPATH_DESC', 'Caminho (path) das Imagens da Categoria. Se seleccionar o caminho "category_images/", o caminho será o seguinte "your_html_rootfolder/images/fbfiles/category_images/');
DEFINE('_KUNENA_ANN_MODID', 'IDs do(s) Moderador(es) dos Comunicados&#32;');
DEFINE('_KUNENA_ANN_MODID_DESC', 'Adicione os IDs dos utilizadores autorizados a moderar os comunicados, p.e. 62,63,73. Os Moderadores de anúncio/comunicado podem adicionar, editar e apagar os anúncios.');
//
DEFINE('_KUNENA_FORUM_TOP', 'Fórum&#32;');
DEFINE('_KUNENA_CHILD_BOARDS', 'Sub-Fórum&#32;');
DEFINE('_KUNENA_QUICKMSG', 'Resposta rápida&#32;');
DEFINE('_KUNENA_THREADS_IN_FORUM', 'Tópicos no Fórum&#32;');
DEFINE('_KUNENA_FORUM', 'Fórum&#32;');
DEFINE('_KUNENA_SPOTS', 'Spotlights');
DEFINE('_KUNENA_CANCEL', 'cancelar');
DEFINE('_KUNENA_TOPIC', 'TÓPICO:&#32;');
DEFINE('_KUNENA_POWEREDBY', 'Powered by&#32;');
// Time Format
DEFINE('_TIME_TODAY', '<b>Hoje</b>&#32;');
DEFINE('_TIME_YESTERDAY', '<b>Ontem</b>&#32;');
//  STARTS HERE!
DEFINE('_KUNENA_WHO_LATEST_POSTS', 'Últimos Posts');
DEFINE('_KUNENA_WHO_WHOISONLINE', 'Quem está Online');
DEFINE('_KUNENA_WHO_MAINPAGE', 'Fórum Principal');
DEFINE('_KUNENA_GUEST', 'Visitante');
DEFINE('_KUNENA_PATHWAY_VIEWING', 'a ver');
DEFINE('_KUNENA_ATTACH', 'Anexo');
// Favorite
DEFINE('_KUNENA_FAVORITE', 'Favoritos');
DEFINE('_USER_FAVORITES', 'Meus Favoritos');
DEFINE('_THREAD_UNFAVORITE', 'Remover dos Favoritos');
// profilebox
DEFINE('_PROFILEBOX_WELCOME', 'Bem vindo');
DEFINE('_PROFILEBOX_SHOW_LATEST_POSTS', 'Mostrar Últimos Posts');
DEFINE('_PROFILEBOX_SET_MYAVATAR', 'Escolher meu Avatar');
DEFINE('_PROFILEBOX_MYPROFILE', 'Meu Perfil');
DEFINE('_PROFILEBOX_SHOW_MYPOSTS', 'Mostrar meus Posts');
DEFINE('_PROFILEBOX_GUEST', 'Visitante');
DEFINE('_PROFILEBOX_LOGIN', 'Login');
DEFINE('_PROFILEBOX_REGISTER', 'Registar');
DEFINE('_PROFILEBOX_LOGOUT', 'Sair');
DEFINE('_PROFILEBOX_LOST_PASSWORD', 'Esqueceu a Palavra-chave?');
DEFINE('_PROFILEBOX_PLEASE', 'Efectuar');
DEFINE('_PROFILEBOX_OR', 'ou');
// recentposts
DEFINE('_RECENT_RECENT_POSTS', 'Posts Recentes');
DEFINE('_RECENT_TOPICS', 'Tópico');
DEFINE('_RECENT_AUTHOR', 'Autor');
DEFINE('_RECENT_CATEGORIES', 'Categorias');
DEFINE('_RECENT_DATE', 'Data');
DEFINE('_RECENT_HITS', 'Hits');
// announcement

DEFINE('_ANN_ANNOUNCEMENTS', 'Comunicados');
DEFINE('_ANN_ID', 'ID');
DEFINE('_ANN_DATE', 'Data');
DEFINE('_ANN_TITLE', 'Título');
DEFINE('_ANN_SORTTEXT', 'Texto curto');
DEFINE('_ANN_LONGTEXT', 'Texto longo');
DEFINE('_ANN_ORDER', 'Ordenar');
DEFINE('_ANN_PUBLISH', 'Publicar');
DEFINE('_ANN_PUBLISHED', 'Publicado');
DEFINE('_ANN_UNPUBLISHED', 'Não publicado');
DEFINE('_ANN_EDIT', 'Editar');
DEFINE('_ANN_DELETE', 'Apagar');
DEFINE('_ANN_SUCCESS', 'Sucesso');
DEFINE('_ANN_SAVE', 'Guardar');
DEFINE('_ANN_YES', 'Sim');
DEFINE('_ANN_NO', 'Não');
DEFINE('_ANN_ADD', 'Adicionar Novo');
DEFINE('_ANN_SUCCESS_EDIT', 'Editado com sucesso');
DEFINE('_ANN_SUCCESS_ADD', 'Adicionado com sucesso');
DEFINE('_ANN_DELETED', 'Apagado com sucesso');
DEFINE('_ANN_ERROR', 'ERRO');
DEFINE('_ANN_READMORE', 'Ler mais...');
DEFINE('_ANN_CPANEL', 'Painel de Controle dos Comunicados');
DEFINE('_ANN_SHOWDATE', 'Mostrar Data');
// Stats
DEFINE('_STAT_FORUMSTATS', 'Estatísticas');
DEFINE('_STAT_GENERAL_STATS', 'Gerais');
DEFINE('_STAT_TOTAL_USERS', 'Total utilizadores');
DEFINE('_STAT_LATEST_MEMBERS', 'Último membro');
DEFINE('_STAT_PROFILE_INFO', 'Ver informação Perfil');
DEFINE('_STAT_TOTAL_MESSAGES', 'Total Mensagens');
DEFINE('_STAT_TOTAL_SUBJECTS', 'Total assuntos');
DEFINE('_STAT_TOTAL_CATEGORIES', 'Total Categorias');
DEFINE('_STAT_TOTAL_SECTIONS', 'Total Secções');
DEFINE('_STAT_TODAY_OPEN_THREAD', 'Abertas Hoje');
DEFINE('_STAT_YESTERDAY_OPEN_THREAD', 'Abertas Ontem');
DEFINE('_STAT_TODAY_TOTAL_ANSWER', 'Total Respostas Hoje');
DEFINE('_STAT_YESTERDAY_TOTAL_ANSWER', 'Total Respostas Ontem');
DEFINE('_STAT_VIEW_RECENT_POSTS_ON_FORUM', 'Ver Posts Recentes');
DEFINE('_STAT_MORE_ABOUT_STATS', 'Mais estatísticas');
DEFINE('_STAT_USERLIST', 'Lista de Utilizadores');
DEFINE('_STAT_TEAMLIST', 'Equipa do fórum');
DEFINE('_STATS_FORUM_STATS', 'Estatísticas');
DEFINE('_STAT_POPULAR', 'Populares');
DEFINE('_STAT_POPULAR_USER_TMSG', 'Utilizadores ( Total Mensagens)&#32;');
DEFINE('_STAT_POPULAR_USER_KGSG', 'Tópicos&#32;');
DEFINE('_STAT_POPULAR_USER_GSG', 'Utilizadores ( Total Vistas Perfil)&#32;');
//Team List
DEFINE('_MODLIST_ONLINE', 'Utilizador Online');
DEFINE('_MODLIST_OFFLINE', 'Utilizador Offline');
// Whoisonline
DEFINE('_WHO_WHOIS_ONLINE', 'Em linha');
DEFINE('_WHO_ONLINE_NOW', 'Online');
DEFINE('_WHO_ONLINE_MEMBERS', 'Membros');
DEFINE('_WHO_AND', 'e');
DEFINE('_WHO_ONLINE_GUESTS', 'Visitantes');
DEFINE('_WHO_ONLINE_USER', 'Utilizador');
DEFINE('_WHO_ONLINE_TIME', 'Hora');
DEFINE('_WHO_ONLINE_FUNC', 'Acção');
// Userlist
DEFINE('_USRL_USERLIST', 'Lista de utilizadores');
DEFINE('_USRL_REGISTERED_USERS', '%s tem <strong>%d</strong> utilizadores registados');
DEFINE('_USRL_SEARCH_ALERT', 'Coloque um valor para a procura!');
DEFINE('_USRL_SEARCH', 'Encontrar utilizador');
DEFINE('_USRL_SEARCH_BUTTON', 'Procurar');
DEFINE('_USRL_LIST_ALL', 'Listar tudo');
DEFINE('_USRL_NAME', 'Nome');
DEFINE('_USRL_USERNAME', 'Nome utilizador');
DEFINE('_USRL_EMAIL', 'E-mail');
DEFINE('_USRL_USERTYPE', 'Tipo de utilizador');
DEFINE('_USRL_JOIN_DATE', 'Data de registo');
DEFINE('_USRL_LAST_LOGIN', 'Último login');
DEFINE('_USRL_NEVER', 'Nunca');
DEFINE('_USRL_BLOCK', 'Estado');
DEFINE('_USRL_MYPMS2', 'MyPMS');
DEFINE('_USRL_ASC', 'Ascendente');
DEFINE('_USRL_DESC', 'Descendente');
DEFINE('_USRL_DATE_FORMAT', '%d/%m/%Y');
DEFINE('_USRL_TIME_FORMAT', '%H:%M');
DEFINE('_USRL_USEREXTENDED', 'Detalhes');
DEFINE('_USRL_COMPROFILER', 'Perfil');
DEFINE('_USRL_THUMBNAIL', 'Imagem');
DEFINE('_USRL_READON', 'Mostrar');
DEFINE('_USRL_MYPMSPRO', 'Clexus PM');
DEFINE('_USRL_MYPMSPRO_SENDPM', 'Enviar PM');
DEFINE('_USRL_JIM', 'PM');
DEFINE('_USRL_UDDEIM', 'PM');
DEFINE('_USRL_SEARCHRESULT', 'Resultados para a procura de');
DEFINE('_USRL_STATUS', 'Estado');
DEFINE('_USRL_LISTSETTINGS', 'Configurações da lista de utilizadores');
DEFINE('_USRL_ERROR', 'Erro');

//changed in 1.1.4 stable
DEFINE('_COM_A_PMS_TITLE', 'Componente de mensagens privadas');
DEFINE('_COM_A_COMBUILDER_TITLE', 'Community Builder');
DEFINE('_FORUM_SEARCH', 'Procurado por: %s');
DEFINE('_MODERATION_DELETE_MESSAGE', 'Tem a certeza que pretende apagar esta mensagem? \n\n NOTA: Se apagar a(s) mensagem(ns) não existe maneira de a(s) recuperar!');
DEFINE('_MODERATION_DELETE_SUCCESS', 'O(s) post(s) foi(ram) apagado(s)');
DEFINE('_COM_A_RANKING', 'Ranking');
DEFINE('_COM_A_BOT_REFERENCE', 'Mostrar o bot do Gráfico de referência');
DEFINE('_COM_A_MOSBOT', 'Activar o bot de discussão');
DEFINE('_PREVIEW', 'Pré-visualizar');
DEFINE('_COM_A_MOSBOT_TITLE', 'Bot de discussão');
DEFINE('_COM_A_MOSBOT_DESC', 'O Bot de discussão permite os seus utilizadores debater o conteúdo dos items nos fóruns. The Content Title is used as the topic subject.'
           . '<br />If a topic does not exist, a new one is created. If the topic already exists, the user is shown the thread and where to reply.' . '<br /><strong>You will need to download and install the bot separately.</strong>'
           . '<br />check the <a href="http://www.Kunena.com">Kunena Site</a> for more information.' . '<br />When installed, you will need to add the following bot lines to your Content:' . '<br />{mos_fb_discuss:<em>catid</em>}'
           . '<br />The <em>catid</em> is the category in which the Content Item can be discussed. To find the proper catid, look into the forums ' . 'and check the category ID from the URL in your browser.'
           . '<br />Example: if you want the article discussed in Forum with catid 26, the bot should look like: {mos_fb_discuss:26}'
           . '<br />This seems a bit difficult, but it does allow you to have each Content Item to be discussed in a matching forum.');
//new in 1.1.4 stable
// search.class.php
DEFINE('_FORUM_SEARCHTITLE', 'Procura');
DEFINE('_FORUM_SEARCHRESULTS', 'A mostrar os %s de %s resultados.');
// Help, FAQ
DEFINE('_COM_FORUM_HELP', 'Ajuda');
// rules.php
DEFINE('_COM_FORUM_RULES', 'Regras');
DEFINE('_COM_FORUM_RULES_DESC', '<ul><li>Editar este ficheiro para inserir as suas regras joomlaroot/administrator/components/com_kunena/language/kunena.english.php</li><li>Regra 2</li><li>Regra 3</li><li>Regra 4</li><li>...</li></ul>');
//smile.class.php
DEFINE('_COM_BOARDCODE', 'Código do Fórum');
// moderate_messages.php
DEFINE('_MODERATION_APPROVE_SUCCESS', 'O(s) post(s) foi(ram) aprovado(s).');
DEFINE('_MODERATION_DELETE_ERROR', 'ERRO: O(s) post(s) não pode(m) ser apagado(s).');
DEFINE('_MODERATION_APPROVE_ERROR', 'ERRO: O(s) post(s) não pode(m) ser aprovado(s).');
// listcat.php
DEFINE('_GEN_NOFORUMS', 'Não existem fóruns nesta categoria!');
//new in 1.1.3 stable
DEFINE('_POST_GHOST_FAILED', 'Falha ao criar tópico fantasma no fórum antigo!');
DEFINE('_POST_MOVE_GHOST', 'Deixar uma cópia da mensagem neste fórum');
//new in 1.1 Stable
DEFINE('_GEN_FORUM_JUMP', 'Ir para Fórum');
DEFINE('_COM_A_FORUM_JUMP', 'Activar ir para Fórum');
DEFINE('_COM_A_FORUM_JUMP_DESC', 'Se escolher &quot;Sim&quot;, será mostrado nas páginas do fórum um selector que permitirá saltar de um fórum ou categoria de uma forma mais rápida.');
//new in 1.1 RC1
DEFINE('_GEN_RULES', 'Regras');
DEFINE('_COM_A_RULESPAGE', 'Activar página de Regras');
DEFINE('_COM_A_RULESPAGE_DESC',
    'Se escolher &quot;Sim&quot;, um link no cabeçalho do Menu irá aparecer para a página de Regras. Esta página pode ser usada para coisas como Regras de utilização do fórum etc. Pode alterar os conteúdos deste ficheiro como você quiser. Para isto basta abrir e editar o ficheiro rules.php em /joomla_root/components/com_Kunena. <em>Certifique-se de ter sempre um backup deste ficheiro pois ele será sobreescrito ao actualizar o fórum!</em>');
DEFINE('_MOVED_TOPIC', 'MOVIDO:');
DEFINE('_COM_A_PDF', 'Activar criação de PDF');
DEFINE('_COM_A_PDF_DESC',
    'Escolha &quot;Sim&quot; se quiser activar a funcionalidade de os utilizadores poderem criar um simples documento de PDF dos conteúdos de um tópico.<br />É um <u>simples</u> Documento PDF; sem mark up, sem enfeites e tal, mas contém todo o texto do tópico.');
DEFINE('_GEN_PDFA', 'Clique neste botão para criar um documento PDF para este tópico (aberto numa nova janela).');
DEFINE('_GEN_PDF', 'Pdf');
//new in 1.0.4 stable
DEFINE('_VIEW_PROFILE', 'Clique aqui para ver o perfil deste utilizador');
DEFINE('_VIEW_ADDBUDDY', 'Clique aqui para adicionar este utilizador à sua lista de amigos');
DEFINE('_POST_SUCCESS_POSTED', 'A sua mensagem foi colocada com sucesso');
DEFINE('_POST_SUCCESS_VIEW', '[ Regressar ao tópico ]');
DEFINE('_POST_SUCCESS_FORUM', '[ Regressar ao fórum ]');
DEFINE('_RANK_ADMINISTRATOR', 'Administrador');
DEFINE('_RANK_MODERATOR', 'Moderador');
DEFINE('_SHOW_LASTVISIT', 'Desde a última visita');
DEFINE('_COM_A_BADWORDS_TITLE', 'Filtro palavras censuradas');
DEFINE('_COM_A_BADWORDS', 'Usar filtro palavras censuradas');
DEFINE('_COM_A_BADWORDS_DESC', 'Escolha &quot;Sim&quot; se quiser que as palavras censuradas sejam filtradas dos Posts. Terá de as definir nas configurações do Componente Badword. Para poder usar isto terá que ter o Componente Badword instalado!');
DEFINE('_COM_A_BADWORDS_NOTICE', '* Esta mensagem foi censurada porque contém uma ou mais palavras que constam nas palavras censuradas pelo Administrador do Fórum *');
DEFINE('_COM_A_AVATAR_SRC', 'Usar imagem do avatar de');
DEFINE('_COM_A_AVATAR_SRC_DESC',
    'Se tiver instalado um dos componentes seguintes: JomSocial, Clexus PM ou Community Builder, pode configurar o Kunena para usar a imagem avatar do perfil de utilizador do JomSocial, Clexus PM ou Community Builder. NOTA: Para o Community Builder você precisa de ter a opção de thumbnail ligada porque o fórum usa thumbnail das figuras e não as originais.');
DEFINE('_COM_A_KARMA', 'Mostrar indicador de Popularidade');
DEFINE('_COM_A_KARMA_DESC', 'Escolha &quot;Sim&quot; para mostrar a Popularidade do Utilizador e botões relacionados (aumentar/diminuir) e se as Estatísticas estiverem activadas.');
DEFINE('_COM_A_DISEMOTICONS', 'Desactivar emoticons');
DEFINE('_COM_A_DISEMOTICONS_DESC', 'Escolha &quot;Sim&quot; para desactivar totalmente os gráficos de emoticons (smiles).');
DEFINE('_COM_C_FBCONFIG', 'Configuração Kunena Fórum');
DEFINE('_COM_C_FBCONFIGDESC', 'Configurar todas as funcionalidades Kunena');
DEFINE('_COM_C_FORUM', 'Gerir Fórum');
DEFINE('_COM_C_FORUMDESC', 'Adicionar e configurar categorias/fóruns');
DEFINE('_COM_C_USER', 'Gerir Utilizadores');
DEFINE('_COM_C_USERDESC', 'Administração do perfil dos utilizadores');
DEFINE('_COM_C_FILES', 'Enviar Ficheiros (Upload)');
DEFINE('_COM_C_FILESDESC', 'Visualizar e administrar ficheiros enviados');
DEFINE('_COM_C_IMAGES', 'Enviar Imagens (Upload)');
DEFINE('_COM_C_IMAGESDESC', 'Visualizar e administrar imagens enviadas');
DEFINE('_COM_C_CSS', 'Editar Ficheiro CSS');
DEFINE('_COM_C_CSSDESC', 'Modificar estilo e visual do Kunena');
DEFINE('_COM_C_SUPPORT', 'Suporte Kunena WebSite');
DEFINE('_COM_C_SUPPORTDESC', 'Ligar ao SiteWeb Kunena (nova janela)');
DEFINE('_COM_C_PRUNETAB', 'Optimizar Fórums');
DEFINE('_COM_C_PRUNETABDESC', 'Remover tópicos antigos (configurável)');
DEFINE('_COM_C_PRUNEUSERS', 'Optimizar Utilizadores'); // <=FB 1.0.3
DEFINE('_COM_C_PRUNEUSERSDESC', 'Sincronizar tabela de utilizador Kunena com tabela de utilizador Joomla!'); // <=FB 1.0.3
DEFINE('_COM_C_LOADMODPOS', 'Carregar posições do módulo');
DEFINE('_COM_C_LOADMODPOSDESC', 'Carregar posições do módulo para o template Kunena');
DEFINE('_COM_C_UPGRADEDESC', 'Actualizar a sua base de dados para a última versão após um upgrade');
DEFINE('_COM_C_BACK', 'Voltar para o Painel de Control Kunena');
DEFINE('_SHOW_LAST_SINCE', 'Tópicos activos desde a última visita em:');
DEFINE('_POST_SUCCESS_REQUEST2', 'O seu pedido foi processado');
DEFINE('_POST_NO_PUBACCESS3', 'Clique aqui para se registar.');
//==================================================================================================
//Changed in 1.0.4
//please update your local language file with these changes as well
DEFINE('_POST_SUCCESS_DELETE', 'A mensagem foi apagada com sucesso.');
DEFINE('_POST_SUCCESS_EDIT', 'A mensagem foi editada com sucesso.');
DEFINE('_POST_SUCCESS_MOVE', 'O tópico foi movido com sucesso.');
DEFINE('_POST_SUCCESS_POST', 'A sua mensagem foi colocada com sucesso.');
DEFINE('_POST_SUCCESS_SUBSCRIBE', 'A sua subscrição foi processada.');
//==================================================================================================
//new in 1.0.3 stable
//Karma
DEFINE('_KARMA', 'Popularidade');
DEFINE('_KARMA_SMITE', 'Diminuir');
DEFINE('_KARMA_APPLAUD', 'Aumentar');
DEFINE('_KARMA_BACK', 'Voltar ao tópico,');
DEFINE('_KARMA_WAIT', 'Só pode modificar a popularidade de um utilizador a cada 6 horas. <br/>Tente modificar de novo a popularidade de um utilizador após ter terminado o período de tempo mencionado.');
DEFINE('_KARMA_SELF_DECREASE', 'Não tente diminuir a sua própria popularidade!');
DEFINE('_KARMA_SELF_INCREASE', 'A sua popularidade foi reduzida por tentar aumentá-la!');
DEFINE('_KARMA_DECREASED', 'Popularidade do utilizador diminuida. Se não regressar dentro de momentos ao tópico,');
DEFINE('_KARMA_INCREASED', 'Popularidade do utilizador aumentada. Se não regressar dentro de momentos ao tópico,');
DEFINE('_COM_A_TEMPLATE', 'Template');
DEFINE('_COM_A_TEMPLATE_DESC', 'Escolha o template a usar.');
DEFINE('_COM_A_TEMPLATE_IMAGE_PATH', 'Configuração das imagens');
DEFINE('_COM_A_TEMPLATE_IMAGE_PATH_DESC', 'Escolha as configurações de imagens de templates para usar.');
DEFINE('_PREVIEW_CLOSE', 'Fechar esta janela');
//==========================================
//new in 1.0 Stable
DEFINE('_COM_A_POSTSTATSBAR', 'Usar Barra de Estatísticas dos Posts');
DEFINE('_COM_A_POSTSTATSBAR_DESC', 'Escolha &quot;Sim&quot; se quiser que o nº de Posts que um utilizador enviou, seja colocado numa barra de estatísticas.');
DEFINE('_COM_A_POSTSTATSCOLOR', 'Número da Cor para a Barra de Estatísticas');
DEFINE('_COM_A_POSTSTATSCOLOR_DESC', 'Escolha o número da cor que pretende usar na Barra de Estatísticas dos Posts');
DEFINE('_LATEST_REDIRECT',
    'O Kunena precisa (re)estabelecer os seus privilégios de acesso antes de poder criar uma lista dos últimos posts.\nNão se preocupe, é normal depois de 30 minutos de inactividade ou depois de se ligar novamente.\nSubmeta de novo o seu pedido de procura.');
DEFINE('_SMILE_COLOUR', 'Cores');
DEFINE('_SMILE_SIZE', 'Tamanho');
DEFINE('_COLOUR_DEFAULT', 'Padrão');
DEFINE('_COLOUR_RED', 'Vermelho');
DEFINE('_COLOUR_PURPLE', 'Púrpura');
DEFINE('_COLOUR_BLUE', 'Azul');
DEFINE('_COLOUR_GREEN', 'Verde');
DEFINE('_COLOUR_YELLOW', 'Amarelo');
DEFINE('_COLOUR_ORANGE', 'Laranja');
DEFINE('_COLOUR_DARKBLUE', 'Azul Escuro');
DEFINE('_COLOUR_BROWN', 'Castanho');
DEFINE('_COLOUR_GOLD', 'Dourado');
DEFINE('_COLOUR_SILVER', 'Prateado');
DEFINE('_SIZE_NORMAL', 'Normal');
DEFINE('_SIZE_SMALL', 'Pequeno');
DEFINE('_SIZE_VSMALL', 'Muito Pequeno');
DEFINE('_SIZE_BIG', 'Grande');
DEFINE('_SIZE_VBIG', 'Muito Grande');
DEFINE('_IMAGE_SELECT_FILE', 'Escolha a imagem para anexar');
DEFINE('_FILE_SELECT_FILE', 'Escolha o ficheiro para anexar');
DEFINE('_FILE_NOT_UPLOADED', 'O seu ficheiro não foi enviado. Tente enviar de novo ou editar o post.');
DEFINE('_IMAGE_NOT_UPLOADED', 'A sua imagem não foi enviada. Tente enviar de novo ou editar o post.');
DEFINE('_BBCODE_IMGPH', 'Inserir [img] no post para a imagem em anexo');
DEFINE('_BBCODE_FILEPH', 'Inserir [file] no post para o ficheiro em anexo');
DEFINE('_POST_ATTACH_IMAGE', '[img]');
DEFINE('_POST_ATTACH_FILE', '[file]');
DEFINE('_USER_UNSUBSCRIBE_ALL', 'Seleccione esta caixa para <strong><u>anular a subscrição</u></strong> de todos os tópicos (inclusivé todos aqueles não visíveis por problemas na submissão)');
DEFINE('_LINK_JS_REMOVED', '<em>O link que continha o Javascript activo foi removido automáticamente.</em>');
//==========================================
//new in 1.0 RC4
DEFINE('_COM_A_LOOKS', 'Estilo e aparência');
DEFINE('_COM_A_USERS', 'Relacionado com Utilizador');
DEFINE('_COM_A_LENGTHS', 'Várias configurações do tamanho');
DEFINE('_COM_A_SUBJECTLENGTH', 'Tamanho máximo do assunto');
DEFINE('_COM_A_SUBJECTLENGTH_DESC',
    'Tamanho máximo para a linha do assunto. O número máximo suportado pela base de dados é de 255 caracteres. Se o seu site está configurado para usar caracteres multi-byte como Unicode, UTF-8 ou non-ISO-8599-x deixe o máximo menor usando esta fórmula:<br/><tt>round_down(255/(maximum character set byte size per character))</tt><br/> Exemplo para UTF-8, para o qual o tamanho max. de bite de caracter por caracter é 4 bytes: 255/4','63.');
DEFINE('_LATEST_THREADFORUM', 'Tópico/Fórum');
DEFINE('_LATEST_NUMBER', 'Novos posts');
DEFINE('_COM_A_SHOWNEW', 'Mostrar Novos posts');
DEFINE('_COM_A_SHOWNEW_DESC', 'Se escolher &quot;Sim&quot;, o Kunena irá mostrar para o Utilizador um indicador para os fóruns que contém novos Posts e quais os Posts que são novos desde a sua última visita.');
DEFINE('_COM_A_NEWCHAR', 'Indicador &quot;de novos posts&quot;');
DEFINE('_COM_A_NEWCHAR_DESC', 'Defina aqui o que deve ser usado para indicar novos Posts (como um &quot;!&quot; ou &quot;Novo!&quot;)');
DEFINE('_LATEST_AUTHOR', 'Autor último post');
DEFINE('_GEN_FORUM_NEWPOST', 'Novos Posts');
DEFINE('_GEN_FORUM_NOTNEW', 'Não existem Novos Posts');
DEFINE('_GEN_UNREAD', 'Tópico não lido');
DEFINE('_GEN_NOUNREAD', 'Tópico lido');
DEFINE('_GEN_MARK_ALL_FORUMS_READ', 'Marcar todos os fóruns como lidos');
DEFINE('_GEN_MARK_THIS_FORUM_READ', 'Marcar este fórum como lido');
DEFINE('_GEN_FORUM_MARKED', 'Todos os posts deste fórum foram marcados como lidos');
DEFINE('_GEN_ALL_MARKED', 'Todos os posts foram marcados como lidos');
DEFINE('_IMAGE_UPLOAD', 'Enviar Imagem');
DEFINE('_IMAGE_DIMENSIONS', 'A imagem a enviar pode ter no máximo (largura x altura - tamanho)');
DEFINE('_IMAGE_ERROR_TYPE', 'Só pode enviar imagens no formato JPEG, GIF, ou PNG');
DEFINE('_IMAGE_ERROR_EMPTY', 'Escolha um ficheiro antes de proceder ao envio');
DEFINE('_IMAGE_ERROR_SIZE', 'A imagem excede o tamanho máximo definido pelo Administrador.');
DEFINE('_IMAGE_ERROR_WIDTH', 'A largura da imagem excede o máximo definido pelo Administrador.');
DEFINE('_IMAGE_ERROR_HEIGHT', 'A altura da imagem excede o máximo definido pelo Administrador.');
DEFINE('_IMAGE_UPLOADED', 'A sua imagem foi enviada com sucesso.');
DEFINE('_COM_A_IMAGE', 'Imagens');
DEFINE('_COM_A_IMGHEIGHT', 'Altura máxima da Imagem');
DEFINE('_COM_A_IMGWIDTH', 'Largura máxima da Imagem');
DEFINE('_COM_A_IMGSIZE', 'Tamanho máximo da Imagem<br/><em>em Kilobytes</em>');
DEFINE('_COM_A_IMAGEUPLOAD', 'Autorizar o envio de imagens aos visitantes');
DEFINE('_COM_A_IMAGEUPLOAD_DESC', 'Escolha &quot;Sim&quot; se quiser que todas as pessoas (acesso público) possam enviar uma imagem.');
DEFINE('_COM_A_IMAGEREGUPLOAD', 'Autorizar o envio de imagens aos Registados');
DEFINE('_COM_A_IMAGEREGUPLOAD_DESC', 'Escolha &quot;Sim&quot; se quiser que só os utilizadores registados e logados possam enviar uma imagem.<br/> NOTA: (Super)administradores e moderadores poderão sempre enviar imagens.');
//New since preRC4-II:
DEFINE('_COM_A_UPLOADS', 'Uploads (Imagens/Ficheiros)');
DEFINE('_FILE_TYPES', 'O ficheiro a enviar pode ter - tamanho máximo');
DEFINE('_FILE_ERROR_TYPE', 'Só está autorizado a enviar ficheiros do tipo:\n');
DEFINE('_FILE_ERROR_EMPTY', 'Escolha um ficheiro antes de enviar');
DEFINE('_FILE_ERROR_SIZE', 'O ficheiro a enviar excede o máximo definido pelo Administrador.');
DEFINE('_COM_A_FILE', 'Ficheiros');
DEFINE('_COM_A_FILEALLOWEDTYPES', 'Tipo de ficheiros autorizados');
DEFINE('_COM_A_FILEALLOWEDTYPES_DESC', 'Especifique aqui quais os tipos de ficheiro que são permitidos enviar. Use vírgula para separar as extensões e letras<strong>minúsculas</strong> sem espaços.<br />Exemplo: zip,txt,exe,htm,html');
DEFINE('_COM_A_FILESIZE', 'Tamanho máximo do ficheiro<br/><em>em Kilobytes</em>');
DEFINE('_COM_A_FILEUPLOAD', 'Autorizar o envio de ficheiros aos visitantes');
DEFINE('_COM_A_FILEUPLOAD_DESC', 'Escolha &quot;Sim&quot; se quiser que todas as pessoas (acesso público) possam enviar um ficheiro.');
DEFINE('_COM_A_FILEREGUPLOAD', 'Autorizar o envio de ficheiros aos Registados');
DEFINE('_COM_A_FILEREGUPLOAD_DESC', 'Escolha &quot;Sim&quot; se quiser que só os utilizadores registados e logados possam enviar um ficheiro.<br/> NOTA: (Super)administradores e moderadores poderão sempre enviar ficheiros.');
DEFINE('_SUBMIT_CANCEL', 'O envio do seu post foi cancelado.');
DEFINE('_HELP_SUBMIT', 'Clique aqui para enviar a sua mensagem');
DEFINE('_HELP_PREVIEW', 'Clique aqui para ver como a sua mensagem será visualizada ao enviá-la');
DEFINE('_HELP_CANCEL', 'Clique aqui para cancelar o envio da sua mensagem');
DEFINE('_POST_DELETE_ATT', 'Se esta caixa estiver seleccionada, todas as imagens e ficheiros anexados aos seus posts, que pretende apagar, também serão apagados (recomendado).');
//new since preRC4-III
DEFINE('_COM_A_USER_MARKUP', 'Mostrar Marca de Editado');
DEFINE('_COM_A_USER_MARKUP_DESC', 'Escolha &quot;Sim&quot; se quiser que uma mensagem editada seja marcada com um texto mostrando que a mensagem foi editada por um Utilizador e quando é que ela foi editada.');
DEFINE('_EDIT_BY', 'Post editado por:');
DEFINE('_EDIT_AT', 'em:');
DEFINE('_UPLOAD_ERROR_GENERAL', 'Ocorreu um erro no envio do seu Avatar. Tente de novo ou notifique o Administrador do sistema.');
DEFINE('_COM_A_IMGB_IMG_BROWSE', 'Visualizador de Imagens enviadas');
DEFINE('_COM_A_IMGB_FILE_BROWSE', 'Visualizador de ficheiros enviados');
DEFINE('_COM_A_IMGB_TOTAL_IMG', 'Número de imagens enviadas');
DEFINE('_COM_A_IMGB_TOTAL_FILES', 'Número de ficheiros enviados');
DEFINE('_COM_A_IMGB_ENLARGE', 'Clique na imagem para ver se está no tamanho máximo');
DEFINE('_COM_A_IMGB_DOWNLOAD', 'Clique na imagem para efectuar download');
DEFINE('_COM_A_IMGB_DUMMY_DESC',
    'A opção &quot;Substituir por fictícia&quot; irá substituir a imagem selecionada por uma imagem fictícia.<br /> Isto permitirá remover o ficheiro actual sem danificar o Post.<br /><small><em>Por favor note que será preciso actualizar a página do seu Browser para ver a imagem fictícia.</em></small>');
DEFINE('_COM_A_IMGB_DUMMY', 'Imagem fictícia actual');
DEFINE('_COM_A_IMGB_REPLACE', 'Substituir pela fictícia');
DEFINE('_COM_A_IMGB_REMOVE', 'Remover tudo');
DEFINE('_COM_A_IMGB_NAME', 'Nome');
DEFINE('_COM_A_IMGB_SIZE', 'Tamanho');
DEFINE('_COM_A_IMGB_DIMS', 'Dimensões');
DEFINE('_COM_A_IMGB_CONFIRM', 'Tem a certeza que deseja apagar este ficheiro? \n Ao apagar um ficheiro irá criar falhas de referência no seu post...Apagar um ficheiro, trará problemas de visualização ao seu Post...');
DEFINE('_COM_A_IMGB_VIEW', 'Abrir post (para editar)');
DEFINE('_COM_A_IMGB_NO_POST', 'Post não referenciado!');
DEFINE('_USER_CHANGE_VIEW', 'As alterações efectuadas nestas definições só ficarão activas da próxima vez que visitar o fórum.');
DEFINE('_MOSBOT_DISCUSS_A', 'Debater este artigo nos fóruns. (');
DEFINE('_MOSBOT_DISCUSS_B', '&#32;posts)');
DEFINE('_POST_DISCUSS', 'Este tópico debate o conteúdo do artigo');
DEFINE('_COM_A_RSS', 'Activar RSS feed');
DEFINE('_COM_A_RSS_DESC', 'O RSS feed habilita Utilizadores a carregar os últimos posts na sua aplicação de leitura de RSS em seu desktop (veja <a href="http://www.rssreader.com" target="_blank">rssreader.com</a> por exemplo.');
DEFINE('_LISTCAT_RSS', 'obter os últimos posts directamente para o seu desktop');
DEFINE('_SEARCH_REDIRECT', 'O Kunena precisa (re)estabelecer os seus privilégios de acesso antes de poder criar uma lista dos últimos posts.\nNão se preocupe, é normal depois de 30 minutos de inactividade ou depois de se ligar novamente.\nSubmeta de novo o seu pedido de procura.');
//====================
//admin.forum.html.php
DEFINE('_COM_A_CONFIG', 'Configuração Kunena Fórum');
DEFINE('_COM_A_DISPLAY', 'Visualizar #');
DEFINE('_COM_A_CURRENT_SETTINGS', 'Configuração actual');
DEFINE('_COM_A_EXPLANATION', 'Descrição');
DEFINE('_COM_A_BOARD_TITLE', 'Título Fórum');
DEFINE('_COM_A_BOARD_TITLE_DESC', 'O nome do seu Fórum');
//Removed Threaded View Option - No longer support in Kunena - It has been broken for years
//DEFINE('_COM_A_VIEW_TYPE', 'Default View type');
//DEFINE('_COM_A_VIEW_TYPE_DESC', 'Choose between a threaded or flat view as default');
DEFINE('_COM_A_THREADS', 'Tópicos por página');
DEFINE('_COM_A_THREADS_DESC', 'Número de tópicos a mostrar por página');
DEFINE('_COM_A_REGISTERED_ONLY', 'Só utilizadores registados');
DEFINE('_COM_A_REG_ONLY_DESC', 'Escolha &quot;Sim&quot; para permitir que só os Utilizadores registados possam utilizar o fórum (ver e publicar), Escolha &quot;Não&quot; para que qualquer Utilizador possa utilizar o fórum.');
DEFINE('_COM_A_PUBWRITE', 'Ler/Gravar Visitantes');
DEFINE('_COM_A_PUBWRITE_DESC', 'Escolha &quot;Sim&quot; para permitir privilégios de gravação públicos. Escolha &quot;Não&quot; para que qualquer Utilizador possa ver o fórum, mas só os utilizadores registados possam publicar Posts.');
DEFINE('_COM_A_USER_EDIT', 'Autorizar edição utilizadores');
DEFINE('_COM_A_USER_EDIT_DESC', 'Escolha &quot;Sim&quot; para permitir a utilizadores registados editarem os seus próprios Posts.');
DEFINE('_COM_A_MESSAGE', 'Para guardar as alterações deve pressionar o botão &quot;Guardar&quot; indicado no topo.');
DEFINE('_COM_A_HISTORY', 'Mostrar Histórico');
DEFINE('_COM_A_HISTORY_DESC', 'Escolha &quot;Sim&quot; se quiser que um histórico do tópico seja mostrado quando uma resposta/citação for feita');
DEFINE('_COM_A_SUBSCRIPTIONS', 'Autorizar Subscrições');
DEFINE('_COM_A_SUBSCRIPTIONS_DESC', 'Escolha &quot;Sim&quot; se quiser que utilizadores registados possam subscrever os tópicos para receber um email de notificação quando uma resposta for colocada');
DEFINE('_COM_A_HISTLIM', 'Limite Histórico');
DEFINE('_COM_A_HISTLIM_DESC', 'Número de posts a mostrar no histórico');
DEFINE('_COM_A_FLOOD', 'Protecção Sobrecarga (Flood)');
DEFINE('_COM_A_FLOOD_DESC', 'Tempo em segundos que um utilizador tem de esperar para publicar dois Posts consecutivos. Escolha 0(zero) para desligar protecção de Flood. NOTA: Protecção de Flood<em>pode</em> causar problemas de performance...');
DEFINE('_COM_A_MODERATION', 'E-mail Moderadores');
DEFINE('_COM_A_MODERATION_DESC',
    'Escolha &quot;Sim&quot; se quiser que notificações de email de novos Posts sejam enviados para o(s) moderador(es) do fórum. Nota: embora o (super)administrador tenha todos os privilégios de Moderador designe-o explicitamente como moderador!');
DEFINE('_COM_A_SHOWMAIL', 'Mostrar E-mail');
DEFINE('_COM_A_SHOWMAIL_DESC', 'Escolha &quot;Não&quot; se quiser que nunca seja visualizado o email de Utilizadores, nem mesmo dos registados.');
DEFINE('_COM_A_AVATAR', 'Autorizar Avatars');
DEFINE('_COM_A_AVATAR_DESC', 'Escolha &quot;Sim&quot; se quiser que utilizadores registados tenham um avatar (administrado através do seu perfil).');
DEFINE('_COM_A_AVHEIGHT', 'Altura Máxima Avatar');
DEFINE('_COM_A_AVWIDTH', 'Largura Máxima Avatar');
DEFINE('_COM_A_AVSIZE', 'Tamanho Máximo Avatar<br/><em>em Kilobytes</em>');
DEFINE('_COM_A_USERSTATS', 'Mostrar Estatísticas Utilizador');
DEFINE('_COM_A_USERSTATS_DESC', 'Escolha &quot;Sim&quot; se quiser mostrar Estatísticas de Utilizadores, tais como nº de Posts colocados por Utilizador, tipo de Utilizador (Administrador, Moderador, Utilizador, etc.).');
DEFINE('_COM_A_AVATARUPLOAD', 'Autorizar envio Avatar');
DEFINE('_COM_A_AVATARUPLOAD_DESC', 'Escolha &quot;Sim&quot; se quiser que utilizadores registados sejam capazes de enviar os seus avatars.');
DEFINE('_COM_A_AVATARGALLERY', 'Utilizar Galeria Avatars');
DEFINE('_COM_A_AVATARGALLERY_DESC', 'Escolha &quot;Sim&quot; se quiser que os utilizadores registados sejam capazes de usar um avatar de uma galeria que você fornecer (components/com_Kunena/avatars/gallery).');
DEFINE('_COM_A_RANKING_DESC', 'Escolha &quot;Sim&quot; se quiser mostrar o ranking dos utilizadores registados baseados no nº de Posts que enviaram. <br/><strong>Note que deve Activar Estatísticas de Utilizadores na Aba Avançada se também quiser mostrar.</strong>');
DEFINE('_COM_A_RANKINGIMAGES', 'Utilizar Imagens de Rank');
DEFINE('_COM_A_RANKINGIMAGES_DESC',
    'Escolha &quot;Sim&quot; se quiser mostrar o ranking dos Utilizadores registados com uma imagem (de components/com_Kunena/ranks). Ao desligar esta opção irá mostrar o texto para aquele rank. Confira a documentação em www.kunena.com para mais informações sobre imagens de ranking.');

//email and stuff
$_COM_A_NOTIFICATION = "Notificação de novo post de";
$_COM_A_NOTIFICATION1 = "Foi colocado um novo post no tópico para o qual subscreveu em";
$_COM_A_NOTIFICATION2 = "Pode gerir as suas subcrições no link 'Meu Perfil' na página inicial do fórum e depois de efectuar o login no site. Também no seu perfil pode anular a subscrição de tópicos.";
$_COM_A_NOTIFICATION3 = "Não responda a esta notificação de email pois foi gerada automáticamente.";
$_COM_A_NOT_MOD1 = "Foi colocado um novo post para o qual está indicado como moderador em";
$_COM_A_NOT_MOD2 = "Veja isto em pormenor depois de efectuar o login no site.";
DEFINE('_COM_A_NO', 'Não');
DEFINE('_COM_A_YES', 'Sim');
DEFINE('_COM_A_FLAT', 'Corrido');
DEFINE('_COM_A_THREADED', 'Por tópicos');
DEFINE('_COM_A_MESSAGES', 'Mensagens por página');
DEFINE('_COM_A_MESSAGES_DESC', 'Número de mensagens a mostrar por página');
//admin; changes from 0.9 to 0.9.1
DEFINE('_COM_A_USERNAME', 'Nome de utilizador');
DEFINE('_COM_A_USERNAME_DESC', 'Escolha &quot;Sim&quot; se quiser que o nome de utilizador (como no login) seja usado em vez do nome real do Utilizador');
DEFINE('_COM_A_CHANGENAME', 'Permitir mudança de Nome');
DEFINE('_COM_A_CHANGENAME_DESC', 'Escolha &quot;Sim&quot; se quiser que os utilizadores registados sejam capazes de mudar o nome a publicar. Se escolher &quot;Não&quot; então os utilizadores registados não serão capazes de editar os seus nomes.');
//admin; changes 0.9.1 to 0.9.2
DEFINE('_COM_A_BOARD_OFFLINE', 'Fórum Offline');
DEFINE('_COM_A_BOARD_OFFLINE_DESC', 'Escolha &quot;Sim&quot; se quiser colocar a secção do Fórum offline. O fórum irá permanecer acessível para o(s) superadministrador(es).');
DEFINE('_COM_A_BOARD_OFFLINE_MES', 'Mensagem de Fórum Offline');
DEFINE('_COM_A_PRUNE', 'Optimizar Fóruns');
DEFINE('_COM_A_PRUNE_NAME', 'Fórum para optimizar:');
DEFINE('_COM_A_PRUNE_DESC',
    'As funções de optimização do Fórum permitem limpar tópicos que não tenham nenhuma mensagem após determinado período de dias. Isto não remove tópicos que estejam colocados como fixos ou que estejam fechados. Estes tópicos devem ser removidos manualmente. Tópicos em fóruns fechados não podem ser apagados.');
DEFINE('_COM_A_PRUNE_NOPOSTS', 'Limpar os tópicos que não tiveram posts nos últimos &#32;');
DEFINE('_COM_A_PRUNE_DAYS', 'dias');
DEFINE('_COM_A_PRUNE_USERS', 'Optimizar Utilizadores'); // <=FB 1.0.3
DEFINE('_COM_A_PRUNE_USERS_DESC',
    'As funções de limpeza de utilizadores permitem apagar utilizadores da Lista do Kunena contra a lista de Utilizadores do Joomla! Irá apagar todos os perfis dos Utilizadores do Kunena que foram apagados da Framework do Joomla. <br/>Quando tiver a certeza que deseja continuar, clique em &quot;Iniciar Limpeza&quot; na barra de menu acima.'); // <=FB 1.0.3
//general
DEFINE('_GEN_ACTION', 'Acção');
DEFINE('_GEN_AUTHOR', 'Autor');
DEFINE('_GEN_BY', 'por');
DEFINE('_GEN_CANCEL', 'Cancelar');
DEFINE('_GEN_CONTINUE', 'Enviar');
DEFINE('_GEN_DATE', 'Data');
DEFINE('_GEN_DELETE', 'Apagar');
DEFINE('_GEN_EDIT', 'Editar');
DEFINE('_GEN_EMAIL', 'E-mail');
DEFINE('_GEN_EMOTICONS', 'Emoticons');
DEFINE('_GEN_FLAT', 'Corrido');
DEFINE('_GEN_FLAT_VIEW', 'Visualização Corrida');
DEFINE('_GEN_FORUMLIST', 'Lista do Fórum');
DEFINE('_GEN_FORUM', 'Fórum');
DEFINE('_GEN_HELP', 'Ajuda');
DEFINE('_GEN_HITS', 'Hits');
DEFINE('_GEN_LAST_POST', 'Último Post');
DEFINE('_GEN_LATEST_POSTS', 'Mostrar últimos posts');
DEFINE('_GEN_LOCK', 'Fechar');
DEFINE('_GEN_UNLOCK', 'Abrir');
DEFINE('_GEN_LOCKED_FORUM', 'O Fórum está fechado');
DEFINE('_GEN_LOCKED_TOPIC', 'O Tópico está fechado');
DEFINE('_GEN_MESSAGE', 'Mensagem');
DEFINE('_GEN_MODERATED', 'O Fórum é moderado. Será revisto antes de ser publicado.');
DEFINE('_GEN_MODERATORS', 'Moderadores');
DEFINE('_GEN_MOVE', 'Mover');
DEFINE('_GEN_NAME', 'Nome');
DEFINE('_GEN_POST_NEW_TOPIC', 'Colocar novo Tópico');
DEFINE('_GEN_POST_REPLY', 'Responder a Tópico');
DEFINE('_GEN_MYPROFILE', 'Meu Perfil');
DEFINE('_GEN_QUOTE', 'Citar');
DEFINE('_GEN_REPLY', 'Responder');
DEFINE('_GEN_REPLIES', 'Respostas');
DEFINE('_GEN_THREADED', 'Por tópicos');
DEFINE('_GEN_THREADED_VIEW', 'Visão por tópico');
DEFINE('_GEN_SIGNATURE', 'Assinatura');
DEFINE('_GEN_ISSTICKY', 'Tópico está fixado.');
DEFINE('_GEN_STICKY', 'Fixado');
DEFINE('_GEN_UNSTICKY', 'Remover fixar');
DEFINE('_GEN_SUBJECT', 'Assunto');
DEFINE('_GEN_SUBMIT', 'Enviar');
DEFINE('_GEN_TOPIC', 'Tópico');
DEFINE('_GEN_TOPICS', 'Tópicos');
DEFINE('_GEN_TOPIC_ICON', 'ícone do tópico');
DEFINE('_GEN_SEARCH_BOX', 'Pesquisar Fórum');
$_GEN_THREADED_VIEW = "Visão por tópico";
$_GEN_FLAT_VIEW = "Visualização corrida";
//avatar_upload.php
DEFINE('_UPLOAD_UPLOAD', 'Enviar');
DEFINE('_UPLOAD_DIMENSIONS', 'O seu ficheiro de imagem pode ter no máximo (largura x altura - tamanho)');
DEFINE('_UPLOAD_SUBMIT', 'Enviar um novo avatar');
DEFINE('_UPLOAD_SELECT_FILE', 'Escolher ficheiro');
DEFINE('_UPLOAD_ERROR_TYPE', 'Só pode usar imagens em formato jpeg, gif ou png');
DEFINE('_UPLOAD_ERROR_EMPTY', 'Escolha um ficheiro antes de enviar');
DEFINE('_UPLOAD_ERROR_NAME', 'A imagem só pode conter caracteres alfanuméricos e sem espaços.');
DEFINE('_UPLOAD_ERROR_SIZE', 'O tamanho da imagem excede o máximo definido pelo Administrador.');
DEFINE('_UPLOAD_ERROR_WIDTH', 'A largura da imagem excede o máximo definido pelo Administrador.');
DEFINE('_UPLOAD_ERROR_HEIGHT', 'A altura da imagem excede o máximo definido pelo Administrador.');
DEFINE('_UPLOAD_ERROR_CHOOSE', "Não escolheu um Avatar da galeria...");
DEFINE('_UPLOAD_UPLOADED', 'O seu Avatar foi enviado.');
DEFINE('_UPLOAD_GALLERY', 'Escolha um Avatar da galeria:');
DEFINE('_UPLOAD_CHOOSE', 'Confirme a escolha');
// listcat.php
DEFINE('_LISTCAT_ADMIN', 'Um administrador deve criá-las primeiro&#32;');
DEFINE('_LISTCAT_DO', 'Eles saberão o que fazer&#32;');
DEFINE('_LISTCAT_INFORM', 'Informe-os e diga-lhes para se apressarem!');
DEFINE('_LISTCAT_NO_CATS', 'Ainda não existem categorias definidas no Fórum.');
DEFINE('_LISTCAT_PANEL', 'Painel de Administração do Joomla OS CMS.');
DEFINE('_LISTCAT_PENDING', 'mensagem(s) pendente(s)');
// moderation.php
DEFINE('_MODERATION_MESSAGES', 'Não existem mensagens pendentes neste fórum.');
// post.php
DEFINE('_POST_ABOUT_TO_DELETE', 'Está prestes a apagar esta mensagem');
DEFINE('_POST_ABOUT_DELETE', '<strong>NOTA:</strong><br/>
-se apagar um tópico do Fórum (o primeiro post na linha) todos os dependentes também serão apagados!
..Considere deixar em branco o post e o nome caso pretenda remover só o conteúdo..
<br/>
- Todos os dependentes de um post normal apagados serão movidos 1 rank acima na hierarquia do tópico.');
DEFINE('_POST_CLICK', 'clique aqui');
DEFINE('_POST_ERROR', 'Não foi possível encontrar o nome de utilizador/email. Não foi encontrado nenhum erro na base de dados');
DEFINE('_POST_ERROR_MESSAGE', 'Verificou-se um erro SQL desconhecido e a sua mensagem não foi colocada/enviada. Se a situação se mantiver, contacte o Administrador, por favor.');
DEFINE('_POST_ERROR_MESSAGE_OCCURED', 'Verificou-se um erro e a sua mensagem não foi actualizada. Tente de novo. Se este erro continuar, contacte o Administrador, por favor.');
DEFINE('_POST_ERROR_TOPIC', 'Ocorreu um erro durante a operação de eliminação. Veja o motivo do erro a seguir:');
DEFINE('_POST_FORGOT_NAME', 'Esqueceu-se de incluir o seu nome. Clique no botão anterior do seu browser&#146s e tente de novo.');
DEFINE('_POST_FORGOT_SUBJECT', 'Esqueceu-se de mencionar o assunto. Clique no botão anterior do seu browser&#146s e tente de novo.');
DEFINE('_POST_FORGOT_MESSAGE', 'Esqueceu-se de escrever a sua mensagem. Clique no botão anterior do seu browser&#146s e tente de novo.');
DEFINE('_POST_INVALID', 'Foi solicitado o ID não válido de um post.');
DEFINE('_POST_LOCK_SET', 'O tópico foi fechado.');
DEFINE('_POST_LOCK_NOT_SET', 'O tópico não pode ser fechado.');
DEFINE('_POST_LOCK_UNSET', 'O tópico foi aberto.');
DEFINE('_POST_LOCK_NOT_UNSET', 'O tópico não pode ser aberto.');
DEFINE('_POST_MESSAGE', 'Coloque uma nova mensagem in&#32;');
DEFINE('_POST_MOVE_TOPIC', 'Mover este tópico para o fórum&#32;');
DEFINE('_POST_NEW', 'Coloque uma nova mensagem em:&#32;');
DEFINE('_POST_NO_SUBSCRIBED_TOPIC', 'A sua subscrição para este tópico não pode ser processada.');
DEFINE('_POST_NOTIFIED', 'Seleccione esta caixa se pretende ser notificado das respostas a este tópico.');
DEFINE('_POST_STICKY_SET', 'Este tópico foi fixado.');
DEFINE('_POST_STICKY_NOT_SET', 'Este tópico não pode ser fixado.');
DEFINE('_POST_STICKY_UNSET', 'Este tópico foi desfixado.');
DEFINE('_POST_STICKY_NOT_UNSET', 'Este tópico não pode ser desfixado.');
DEFINE('_POST_SUBSCRIBE', 'Subscrever');
DEFINE('_POST_SUBSCRIBED_TOPIC', 'Efectuou a subscrição deste tópico.');
DEFINE('_POST_SUCCESS', 'Enviou com sucesso a sua mensagem');
DEFINE('_POST_SUCCES_REVIEW', 'A sua mensagem foi enviada com sucesso. Será revista por um moderador antes de ser publicada no fórum.');
DEFINE('_POST_SUCCESS_REQUEST', 'O seu pedido foi processado. Se não regressar ao tópico dentro de momentos,');
DEFINE('_POST_TOPIC_HISTORY', 'Histórico do tópico de');
DEFINE('_POST_TOPIC_HISTORY_MAX', 'Max. a ver o último');
DEFINE('_POST_TOPIC_HISTORY_LAST', 'posts  -  <i>(último post em primeiro)</i>');
DEFINE('_POST_TOPIC_NOT_MOVED', 'O seu tópico não pode ser movido. Regressar ao tópico:');
DEFINE('_POST_TOPIC_FLOOD1', 'O administrador deste fórum activou o sistema de protecção de sobrecarga. Deve esperar&#32;');
DEFINE('_POST_TOPIC_FLOOD2', '&#32;segundos antes de poder colocar outro post.');
DEFINE('_POST_TOPIC_FLOOD3', 'Clique no botão retroceder do seu browser&#146s para voltar ao fórum.');
DEFINE('_POST_EMAIL_NEVER', 'o seu endereço de e-mail não estará visível no site.');
DEFINE('_POST_EMAIL_REGISTERED', 'o seu endereço de e-mail só estará visível a utilizadores registados.');
DEFINE('_POST_LOCKED', 'Fechado pelo Administrador.');
DEFINE('_POST_NO_NEW', 'Não estão autorizadas novas respostas.');
DEFINE('_POST_NO_PUBACCESS1', 'O Administrador desactivou o acesso público às mensagens.');
DEFINE('_POST_NO_PUBACCESS2', 'Só utilizadores logados/registados<br /> estão autorizados a colocar posts no fórum.');
// showcat.php
DEFINE('_SHOWCAT_NO_TOPICS', '>> Ainda não existem tópicos neste fórum <<');
DEFINE('_SHOWCAT_PENDING', 'mensagem(s) pendente(s)');
// userprofile.php
DEFINE('_USER_DELETE', '&#32;seleccione esta caixa para apagar a sua assinatura');
DEFINE('_USER_ERROR_A', 'Chegou a esta página por erro. Por favor, informe o administrador em qual dos links&#32;');
DEFINE('_USER_ERROR_B', 'clicou para chegar aqui. Informe o Administrador sobre este erro.');
DEFINE('_USER_ERROR_C', 'Muito Obrigado!');
DEFINE('_USER_ERROR_D', 'Número do Erro a incluir no reporte:&#32;');
DEFINE('_USER_GENERAL', 'Opções gerais do Perfil');
DEFINE('_USER_MODERATOR', 'Está indicado como moderador dos fóruns');
DEFINE('_USER_MODERATOR_NONE', 'Não está indicado para nenhum fórum');
DEFINE('_USER_MODERATOR_ADMIN', 'Administradores são moderadores em todos os fóruns.');
DEFINE('_USER_NOSUBSCRIPTIONS', 'Não foram encontradas subscrições para si');
//DEFINE('_USER_PREFERED', 'Prefered Viewtype');
DEFINE('_USER_PROFILE', 'Perfil para&#32;');
DEFINE('_USER_PROFILE_NOT_A', 'O seu perfil&#32;');
DEFINE('_USER_PROFILE_NOT_B', 'não pode');
DEFINE('_USER_PROFILE_NOT_C', '&#32;ser actualizado.');
DEFINE('_USER_PROFILE_UPDATED', 'O seu perfil foi actualizado.');
DEFINE('_USER_RETURN_A', 'Se não regressar dentro de momentos ao seu perfil&#32;');
DEFINE('_USER_RETURN_B', 'clique aqui');
DEFINE('_USER_SUBSCRIPTIONS', 'As suas subscrições');
DEFINE('_USER_UNSUBSCRIBE', 'Anular subscrição');
DEFINE('_USER_UNSUBSCRIBE_A', 'Você&#32;');
DEFINE('_USER_UNSUBSCRIBE_B', 'não');
DEFINE('_USER_UNSUBSCRIBE_C', '&#32;pode anular a subscrição deste tópico.');
DEFINE('_USER_UNSUBSCRIBE_YES', 'Anulou a sua subscrição deste tópico.');
DEFINE('_USER_DELETEAV', '&#32;seleccione esta caixa para apagar o Avatar');
//New 0.9 to 1.0
DEFINE('_USER_ORDER', 'Ordenação preferida para os Posts');
DEFINE('_USER_ORDER_DESC', 'O último post em primeiro lugar');
DEFINE('_USER_ORDER_ASC', 'O primeiro post em primeiro lugar');
// view.php
DEFINE('_VIEW_DISABLED', 'O Administrador desactivou o acesso público de escrita.');
DEFINE('_VIEW_POSTED', 'Colocado por');
DEFINE('_VIEW_SUBSCRIBE', ':: Subscreva este tópico ::');
DEFINE('_MODERATION_INVALID_ID', 'O ID do post requerido é inválido.');
DEFINE('_VIEW_NO_POSTS', 'Não existem posts neste fórum.');
DEFINE('_VIEW_VISITOR', 'Visitante');
DEFINE('_VIEW_ADMIN', 'Administrador');
DEFINE('_VIEW_USER', 'Utilizador');
DEFINE('_VIEW_MODERATOR', 'Moderador');
DEFINE('_VIEW_REPLY', 'Responder a esta mensagem');
DEFINE('_VIEW_EDIT', 'Editar esta mensagem');
DEFINE('_VIEW_QUOTE', 'Citar esta mensagem no novo post');
DEFINE('_VIEW_DELETE', 'Apagar esta mensagem');
DEFINE('_VIEW_STICKY', 'Colocar este tópico como fixo');
DEFINE('_VIEW_UNSTICKY', 'Desfixar este tópico');
DEFINE('_VIEW_LOCK', 'Fechar este tópico');
DEFINE('_VIEW_UNLOCK', 'Abrir este tópico');
DEFINE('_VIEW_MOVE', 'Mover este tópico para outro fórum');
DEFINE('_VIEW_SUBSCRIBETXT', 'Subscreva este tópico e será notificado por email de novos posts');
//NEW-STRINGS-FOR-TRANSLATING-READY-FOR-SIMPLEBOARD 9.2
DEFINE('_HOME', 'Fórum');
DEFINE('_POSTS', 'Posts:');
DEFINE('_TOPIC_NOT_ALLOWED', 'Posts');
DEFINE('_FORUM_NOT_ALLOWED', 'Fórum');
DEFINE('_FORUM_IS_OFFLINE', 'O Fórum está OFFLINE!');
DEFINE('_PAGE', 'Página:&#32;');
DEFINE('_NO_POSTS', 'Não há Posts');
DEFINE('_CHARS', 'Máximo Caracteres');
DEFINE('_HTML_YES', 'HTML não activo');
DEFINE('_YOUR_AVATAR', '<b>O seu Avatar</b>');
DEFINE('_NON_SELECTED', 'Ainda não seleccionado <br />');
DEFINE('_SET_NEW_AVATAR', 'Escolha um novo avatar');
DEFINE('_THREAD_UNSUBSCRIBE', 'Anular subscrição');
DEFINE('_SHOW_LAST_POSTS', 'Últimos tópicos activos');
DEFINE('_SHOW_HOURS', 'horas');
DEFINE('_SHOW_POSTS', 'Total:&#32;');
DEFINE('_DESCRIPTION_POSTS', 'Os posts novos para os tópicos activos são mostrados');
DEFINE('_SHOW_4_HOURS', '4 Horas');
DEFINE('_SHOW_8_HOURS', '8 Horas');
DEFINE('_SHOW_12_HOURS', '12 Horas');
DEFINE('_SHOW_24_HOURS', '24 Horas');
DEFINE('_SHOW_48_HOURS', '48 Horas');
DEFINE('_SHOW_WEEK', 'Semana');
DEFINE('_POSTED_AT', 'Colocado em');
DEFINE('_DATETIME', 'Y/m/d H:i');
DEFINE('_NO_TIMEFRAME_POSTS', 'Não existem novos posts no espaço de tempo seleccionado.');
DEFINE('_MESSAGE', 'Mensagem');
DEFINE('_NO_SMILIE', 'não');
DEFINE('_FORUM_UNAUTHORIZIED', 'Só poderá colocar posts neste fórum se estiver logado/registado no site.');
DEFINE('_FORUM_UNAUTHORIZIED2', 'Se é utilizador registado efectue Login.');
DEFINE('_MESSAGE_ADMINISTRATION', 'Moderação');
DEFINE('_MOD_APPROVE', 'Aprovar');
DEFINE('_MOD_DELETE', 'Apagar');
//NEW in RC1
DEFINE('_SHOW_LAST', 'Mostrar a mensagem mais recente');
DEFINE('_POST_WROTE', 'escrito');
DEFINE('_COM_A_EMAIL', 'Endereço E-mail do Fórum');
DEFINE('_COM_A_EMAIL_DESC', 'Este é o endereço de email do Fórum. Assegure-se de que está activo e é válido.');
DEFINE('_COM_A_WRAP', 'Tamanho das Palavras Compridas/Longas');
DEFINE('_COM_A_WRAP_DESC',
    'Coloque o número máximo de caracteres que uma palavra deve ter. Este recurso permite delimitar a largura dos Posts para serem ajustadas ao seu.<br />70 caracteres é o máximo de caracteres para templates com largura fixa mas precisa de realizar algumas experiências.<br/>URLs, não interessa o quanto, não são afectados pelo wordwrap');
DEFINE('_COM_A_SIGNATURE', 'Tamanho máximo da assinatura');
DEFINE('_COM_A_SIGNATURE_DESC', 'Número máximo de caracteres permitido para a assinatura do utilizador.');
DEFINE('_SHOWCAT_NOPENDING', 'Não existe(m) mensagem(s) pendente(s)');
DEFINE('_COM_A_BOARD_OFSET', 'Horário de Compensação do Fórum');
DEFINE('_COM_A_BOARD_OFSET_DESC', 'Alguns utilizadores estão a visualizar os fóruns estando localizados em servidores com diferentes fusos-horários. Configure o tempo de compensação que o Kunena deve utilizar em horas. Podem ser usados números negativos e positivos');
//New in RC2
DEFINE('_COM_A_BASICS', 'Básica');
DEFINE('_COM_A_FRONTEND', 'Frontend');
DEFINE('_COM_A_SECURITY', 'Segurança');
DEFINE('_COM_A_AVATARS', 'Avatars');
DEFINE('_COM_A_INTEGRATION', 'Integração');
DEFINE('_COM_A_PMS', 'Activar mensagens privadas');
DEFINE('_COM_A_PMS_DESC',
    'Escolha o componente de &quotprivate messaging&quot se tiver um instalado. Se escolher o Clexus PM também activará o perfil de utilizador e as opções relacionadas (tais como ICQ, AIM, Yahoo, MSN e os links de perfil se o template Kunena utilizado o suportar).');
DEFINE('_VIEW_PMS', 'Clique aqui para enviar uma mensagem privada para este utilizador');
//new in RC3
DEFINE('_POST_RE', 'Re:');
DEFINE('_BBCODE_BOLD', 'Texto a negrito: [b]texto[/b]&#32;');
DEFINE('_BBCODE_ITALIC', 'Texto em itálico: [i]texto[/i]');
DEFINE('_BBCODE_UNDERL', 'Texto sublinhado: [u]texto[/u]');
DEFINE('_BBCODE_QUOTE', 'Texto citado: [quote]texto[/quote]');
DEFINE('_BBCODE_CODE', 'Visualização de código: [code]código[/code]');
DEFINE('_BBCODE_ULIST', 'Lista não ordenada: [ul] [li]text[/li] [/ul] - Sugestão: a lista deverá conter items');
DEFINE('_BBCODE_OLIST', 'Lista ordenada: [ol] [li]texto[/li] [/ol] - Sugestão: a lista deverá conter items');
DEFINE('_BBCODE_IMAGE', 'Imagem: [img size=(01-499)]http://www.google.com/images/web_logo_left.gif[/img]');
DEFINE('_BBCODE_LINK', 'Link: [url=http://www.zzz.com/]Este é o link[/url]');
DEFINE('_BBCODE_CLOSA', 'Fechar todas as tags');
DEFINE('_BBCODE_CLOSE', 'Fechar todas as tags do código bbCode');
DEFINE('_BBCODE_COLOR', 'Cor: [color=#FF6600]texto[/color]');
DEFINE('_BBCODE_SIZE', 'Tamanho: [size=1]texto do tamanho[/size] - Sugestão: tamanhos entre 1 e 5');
DEFINE('_BBCODE_LITEM', 'Listar Itens: [li] listar itens [/li]');
DEFINE('_BBCODE_HINT', 'Ajuda bbCode - Sugestão: bbCode pode ser usado numa selecção de texto!');
DEFINE('_COM_A_TAWIDTH', 'Largura Área Texto');
DEFINE('_COM_A_TAWIDTH_DESC', 'Ajustar a largura do texto da resposta/post para coincidir com o template. <br/>A barra de Emoticon será colocada em 2 linhas se a largura for <= 420 pixels');
DEFINE('_COM_A_TAHEIGHT', 'Altura Área Texto');
DEFINE('_COM_A_TAHEIGHT_DESC', 'Ajustar a altura do texto da resposta/post para coincidir com o template');
DEFINE('_COM_A_ASK_EMAIL', 'Necessário E-mail');
DEFINE('_COM_A_ASK_EMAIL_DESC', 'Necessário um email quando os utilizadores ou visitantes colocarem um post. Escolher &quot;Não&quot; se pretender que esta funcionalidade não se verifique no frontend. Não será perguntado pelo endereço de email aos Postadores.');

//Rank Administration - Dan Syme/IGD
define('_KUNENA_RANKS_MANAGE', 'Gestão dos Ranks');
define('_KUNENA_SORTRANKS', 'Organizar por Ranks');

define('_KUNENA_RANKSIMAGE', 'Imagem do Rank');
define('_KUNENA_RANKS', 'Título do Rank');
define('_KUNENA_RANKS_SPECIAL', 'Especial');
define('_KUNENA_RANKSMIN', 'Limite Mínimo de Posts');
define('_KUNENA_RANKS_ACTION', 'Acções');
define('_KUNENA_NEW_RANK', 'Novo Rank');

?>
