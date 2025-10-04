<?php

//Nao sera mais usado
// use App\routes\Route;
// use App\routes\Request;

// //=====================================================>>>
// //Rotas GET Usuario

// Route::get('/'      ,'UserController/index'       ,'App\\controllers\\user');
// Route::get('/login' ,'UserController/viewLogin'   ,'App\\controllers\\user');
// Route::get('/home'  ,'UserController/viewHome'    ,'App\\controllers\\user');
// Route::get('/logout','UserController/logout'      ,'App\\controllers\\user');

// Route::get('/listaUsuario'                       ,'UserController/loadViewList'       ,'App\\controllers\\user');
// Route::get('/listaUsuario/novoUsuario'           ,'UserController/loadViewRegisterNew','App\\controllers\\user');
// Route::get('/listaUsuario/{id}/atualizarUsuario' ,'UserController/loadRegister'       ,'App\\controllers\\user');
// Route::get('/listaUsuario/{id}/inativarRegistro' ,'UserController/remove'             ,'App\\controllers\\user');

// //Rotas POST Usuario
// Route::post('/listar','UserController/loadViewList','App\\controllers\\user');
// Route::post('/login' ,'UserController/login'       ,'App\\controllers\\user');

// Route::post('/listaUsuario'            ,'UserController/list'        ,'App\\controllers\\user');
// Route::post('/listaUsuario/store'      ,'UserController/store'       ,'App\\controllers\\user');
// Route::post('/listaUsuario/{id}/update','UserController/update'      ,'App\\controllers\\user');
// Route::post('/pesquisarIdUsuario'      ,'UserController/quickSearch' ,'App\\controllers\\user');
// Route::post('/pesquisarIdlistaUsuario' ,'UserController/quickSearch' ,'App\\controllers\\user');
// Route::post('/listaRegistrosUsuario'   ,'UserController/listWithRows','App\\controllers\\user');

// //=====================================================>>>
// //Rotas GET faixa

// Route::get('/listaFaixa'                       ,'RangeController/loadViewList'        ,'App\\controllers\\range');
// Route::get('/listaFaixa/novoFaixa'             ,'RangeController/loadViewNewRegister' ,'App\\controllers\\range');
// Route::get('/listaFaixa/{id}/atualizarFaixa'   ,'RangeController/loadRegister'        ,'App\\controllers\\range');
// Route::get('/listaFaixa/{id}/inativarRegistro' ,'RangeController/remove'              ,'App\\controllers\\range');


// //Rotas POST faixa
// Route::post('/listaFaixa'             ,'RangeController/list'        ,'App\\controllers\\range');
// Route::post('/listaFaixa/store'       ,'RangeController/store'       ,'App\\controllers\\range');
// Route::post('/listaFaixa/{id}/update' ,'RangeController/update'      ,'App\\controllers\\range');
// Route::post('/pesquisarIdFaixa'       ,'RangeController/quickSearch' ,'App\\controllers\\range');
// Route::post('/pesquisarIdlistaFaixa'  ,'RangeController/quickSearch' ,'App\\controllers\\range');


// //=====================================================>>>
// //Rotas GET faixa financeira

// Route::get('/listaFaixaFinanceira'                               ,'FinancialRangeController/loadViewList'        ,'App\\controllers\\range');
// Route::get('/listaFaixaFinanceira/novoFaixaFinanceira'           ,'FinancialRangeController/loadViewNewRegister' ,'App\\controllers\\range');
// Route::get('/listaFaixaFinanceira/{id}/atualizarFaixaFinanceira' ,'FinancialRangeController/loadRegister'        ,'App\\controllers\\range');
// Route::get('/listaFaixaFinanceira/{id}/inativarRegistro'         ,'FinancialRangeController/remove'              ,'App\\controllers\\range');


// //Rotas POST faixa financeira
// Route::post('/listaFaixaFinanceira'            ,'FinancialRangeController/list'       ,'App\\controllers\\range');
// Route::post('/listaFaixaFinaceria/store'       ,'FinancialRangeController/store'      ,'App\\controllers\\range');
// Route::post('/listaFaixaFinanceira/{id}/update','FinancialRangeController/update'     ,'App\\controllers\\range');
// Route::post('/pesquisarIdFaixaFinanceira'      ,'FinancialRangeController/quickSearch','App\\controllers\\range');
// Route::post('/pesquisarIdlistaFaixaFinanceira' ,'FinancialRangeController/quickSearch','App\\controllers\\range');

// //=====================================================>>>
// //Rotas GET CEP
// Route::get('/listaCEP'                             ,'CEPController/loadViewList'        ,'App\\controllers\\CEP');
// Route::get('/listaCEP/novoCEP'                     ,'CEPController/loadViewNewRegister' ,'App\\controllers\\CEP');
// Route::get('/listaCEP/{id}/atualizarCEP'           ,'CEPController/loadRegister'        ,'App\\controllers\\CEP');

// //Rotas POST CEP
// Route::post('/listaCEP'                             ,'CEPController/list'         ,'App\\controllers\\CEP');
// Route::post('/listaCEP/modal/listaParaUsuario'      ,'CEPController/listToUser'   ,'App\\controllers\\CEP');
// Route::post('/listaCEP/store'                       ,'CEPController/store'        ,'App\\controllers\\CEP');
// Route::post('/listaCEP/{id}/update'                 ,'CEPController/update'       ,'App\\controllers\\CEP');
// Route::post('/pesquisarIdCEP'                       ,'CEPController/quickSearch'  ,'App\\controllers\\CEP');
// Route::post('/pesquisarIdlistaCEP'                  ,'CEPController/quickSearch'  ,'App\\controllers\\CEP');
// Route::post('/pesquisarCEP'                         ,'CEPController/quickSearch'  ,'App\\controllers\\CEP');
// Route::post('/carregarModal'                        ,'CEPController/loadModal'    ,'App\\controllers\\CEP');

// //=====================================================>>>
// //Rotas GET IBGE
// Route::get('/listaIBGE'                   ,'IBGEController/loadViewList'       ,'App\\controllers\\CEP');
// Route::get('/listaIBGE/novoIBGE'          ,'IBGEController/loadViewNewRegister','App\\controllers\\CEP');
// Route::get('/listaIBGE/{id}/atualizarIBGE','IBGEController/loadRegister'       ,'App\\controllers\\CEP');

// //Rotas POST IBGE
// Route::post('/listaIBGE'            ,'IBGEController/list'        ,'App\\controllers\\CEP');
// Route::post('/listaIBGE/store'      ,'IBGEController/store'      ,'App\\controllers\\CEP');
// Route::post('/listaIBGE/{id}/update','IBGEController/update'     ,'App\\controllers\\CEP');
// Route::post('/pesquisarIdIBGE'      ,'IBGEController/quickSearch','App\\controllers\\CEP');
// Route::post('/pesquisarIdlistaIBGE' ,'IBGEController/quickSearch','App\\controllers\\CEP');

// //=====================================================>>>
// //Rotas GET Pais
// Route::get('/listaPais'                   ,'CountryController/loadViewList'       ,'App\\controllers\\CEP');
// Route::get('/listaPais/novoPais'          ,'CountryController/loadViewNewRegister','App\\controllers\\CEP');
// Route::get('/listaPais/{id}/atualizarPais','CountryController/loadRegister'       ,'App\\controllers\\CEP');

// //Rotas POST Pais
// Route::post('/listaPais'            ,'CountryController/list'       ,'App\\controllers\\CEP');
// Route::post('/listaPais/store'      ,'CountryController/store'      ,'App\\controllers\\CEP');
// Route::post('/listaPais/{id}/update','CountryController/update'     ,'App\\controllers\\CEP');
// Route::post('/pesquisarIdPais'      ,'CountryController/quickSearch','App\\controllers\\CEP');
// Route::post('/pesquisarIdlistaPais' ,'CountryController/quickSearch','App\\controllers\\CEP');


// //=====================================================>>>
// //Rotas GET Profissoes
// Route::get('/listaProfissao'                         ,'ProfessionController/loadViewList'       ,'App\\controllers\\profession');
// Route::get('/listaProfissao/novoProfissao'           ,'ProfessionController/loadViewNewRegister','App\\controllers\\profession');
// Route::get('/listaProfissao/{id}/atualizarProfissao' ,'ProfessionController/loadRegister'       ,'App\\controllers\\profession');
// Route::get('/listaProfissao/{id}/inativarRegistro'   ,'ProfessionController/remove'             ,'App\\controllers\\profession');

// //Rotas POST Profissoa
// Route::post('/listaProfissao'            ,'ProfessionController/list'       ,'App\\controllers\\profession');
// Route::post('/listaProfissao/store'      ,'ProfessionController/store'      ,'App\\controllers\\profession');
// Route::post('/listaProfissao/{id}/update','ProfessionController/update'     ,'App\\controllers\\profession');
// Route::post('/pesquisarIdProfissao'      ,'ProfessionController/quickSearch','App\\controllers\\profession');
// Route::post('/pesquisarIdlistaProfissao' ,'ProfessionController/quickSearch','App\\controllers\\profession');

// //====================================================================

// /**
//  * Executa a rota 
//  */
// Route::resolve(new Request());
