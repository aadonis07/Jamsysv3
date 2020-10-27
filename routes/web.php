<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Auth::routes(['register' => false]);

Route::group(['middleware' => ['auth']], function () {
    Route::get('/home', function () {
        return redirect(route('validate-dashboard'));
    })->name('home'); // controls the redirection per department
    Route::get('/', 'HomeController@index')->name('validate-dashboard');
    /**
     * NOTE *
     *  In middleware, please specify the department code based on database. Invalid code wiill redirect to login again.
        Please update if needed.
        REFEFENCES: ( LIST BELOW , DONE ROUTE,BLADE,CONTROLLER)
        CODE: [ IT ] = IT DEPARTMENT
        CODE: [ SLS ] = SALES DEPARTMENT
        CODE: [ HR ] = HR DEPARTMENT
        CODE: [ ACCTG ] = ACCOUNTING DEPARTMENT
        CODE: [ ADMIN ] = ADMIN DEPARTMENT
        CODE: [ FTO ] = FITOUT DEPARTMENT
        CODE: [ PLT ] = PLANT DEPARTMENT
        CODE: [ CST ] = CUSTOMER SERVICE DEPARTMENT
        CODE: [ PROD ] = PRODUCTION DEPARTMENT
        CODE: [ WHSE ] = WAREHOUSE (BICUTAN) DEPARTMENT
        CODE: [ MKTG ] = MARKETING DEPARTMENT
        CODE: [ DSGN ] = DESIGN DEPARTMENT
        CODE: [ PRPTR ] = PROPRIETOR DEPARTMENT
        CODE: [ LGCS ] = LOGISTICS DEPARTMENT
        CODE: [ LGCS-WRM ] = LOGISTICS WAREHOUSE (RAW) DEPARTMENT
        CODE: [ LGCS-WSUPP ] = LOGISTICS WAREHOUSE (SUPPLY) DEPARTMENT
        CODE: [ PHR ] = PURCHASING DEPARTMENT
        CODE: [ PUR-RM ] = PURCHASING (RAW) DEPARTMENT
        CODE: [ PUR-SUPP ] = PURCHASING (SUPPLY) DEPARTMENT
     *
     */
    //IT DEPARTMENT
    Route::group(['middleware' => 'department:IT'], function(){
        Route::prefix('/it-department')->group(function(){
            Route::get('/', 'It\DashboardController@showIndex')->name('it-dashboard');
            Route::prefix('products')->group(function(){
                Route::get('/', 'It\ProductController@showIndex')->name('product-list');
                Route::get('/create', 'It\ProductController@showAddproduct')->name('product-create');
                Route::get('/create-raw', 'It\ProductController@showAddproductRaw')->name('product-create-raw');
				Route::get('/create-fit-out', 'It\ProductController@showAddproductFitOut')->name('product-create-fit-out');
                Route::get('/update', 'It\ProductController@showUpdateproduct')->name('product-update-details');
                Route::get('/logs', 'It\ProductController@showProductLogs')->name('product-logs-details');
                Route::prefix('variants')->group(function(){
                    Route::get('/', 'It\ProductController@showVariants')->name('product-variants');
                });
                Route::get('/swatch-details', 'It\ProductController@showSwatchDetails')->name('product-swatch-details');
                Route::post('/{id}', 'It\ProductController@showFunctions')->name('product-functions');
            });
            Route::prefix('settings')->group(function(){
                Route::prefix('departments')->group(function(){
                    Route::get('/', 'It\SettingsController@showDepartments')->name('settings-departments');
                    Route::get('/logs', 'It\SettingsController@showDepartmentLogs')->name('settings-department-logs-details');
                    Route::get('update-department-content', 'It\SettingsController@departmentContent')->name('department-content');
                    Route::prefix('positions')->group(function(){
                        Route::get('/', 'It\SettingsController@showPositions')->name('settings-positions');
                        Route::get('/logs', 'It\SettingsController@showPositionLogs')->name('settings-position-logs-details');
                        Route::get('update-position-content', 'It\SettingsController@positionContent')->name('position-content');
                    });
                });
                Route::prefix('categories')->group(function(){
                    Route::get('/', 'It\SettingsController@showCategories')->name('settings-categories');
                    Route::get('/logs', 'It\SettingsController@showCategoryLogs')->name('settings-category-logs-details');
                    Route::prefix('sub-categories')->group(function(){
                        Route::get('/', 'It\SettingsController@showSubCategories')->name('settings-sub-categories');
                        Route::get('/logs', 'It\SettingsController@showSubCategoryLogs')->name('settings-sub-category-logs-details');
                        Route::prefix('swatches')->group(function(){
                            Route::get('/', 'It\SettingsController@showSwatchesGroups')->name('settings-sub-category-swatches');
                            Route::get('/details', 'It\SettingsController@showSubCategorySwatcheDetails')->name('settings-sub-category-swatch-details');
                        });
                    });
                });
                Route::prefix('swatches')->group(function(){
                    Route::get('/', 'It\SettingsController@showSwatches')->name('settings-swatches');
                });
                Route::prefix('teams')->group(function(){
                    Route::get('/', 'It\SettingsController@showTeams')->name('settings-teams');
                    Route::get('update-team-content', 'It\SettingsController@teamContent')->name('team-content');
                    Route::get('change-manager-content', 'It\SettingsController@changeTeamManagerContent')->name('change-team-manager-content');
                    Route::get('/logs', 'It\SettingsController@showTeamLogs')->name('settings-team-logs-details');
                });
                Route::prefix('industries')->group(function(){
                    Route::get('/', 'It\SettingsController@showIndustries')->name('settings-industries');
                    Route::get('update-industry-content', 'It\SettingsController@industryContent')->name('industry-content');
                    Route::get('/logs', 'It\SettingsController@showIndustryLogs')->name('settings-industry-logs-details');
                });
                Route::prefix('quotation-terms')->group(function(){
                    Route::get('/', 'It\SettingsController@showQuotationTerms')->name('settings-quotation-terms');
                    Route::get('update-quotation-term-content', 'It\SettingsController@quotationTermContent')->name('quotation-term-content');
                    Route::get('/logs', 'It\SettingsController@showQuotationTermLogs')->name('settings-quotation-term-logs-details');
                });
                Route::prefix('business-styles')->group(function(){
                    Route::get('/', 'It\SettingsController@showBusinessStyles')->name('settings-business-styles');
                    Route::get('update-business-style-content', 'It\SettingsController@businessStyleContent')->name('business-style-content');
                    Route::get('/logs', 'It\SettingsController@showBusinessStyleLogs')->name('settings-business-style-logs-details');
                });
                Route::prefix('company-branches')->group(function(){
                    Route::get('/', 'It\SettingsController@showCompanyBranches')->name('settings-company-branches');
                    Route::get('update-company-branch-content', 'It\SettingsController@companyBranchContent')->name('company-branch-content');
                    Route::get('/logs', 'It\SettingsController@showCompanyBranchLogs')->name('settings-company-branch-logs-details');
                });
                Route::prefix('regions')->group(function(){
                    Route::get('/', 'It\SettingsController@showRegions')->name('settings-regions');
                    Route::get('update-region-content', 'It\SettingsController@regionContent')->name('region-content');
                    Route::get('/logs', 'It\SettingsController@showRegionLogs')->name('settings-region-logs-details');
                    Route::prefix('provinces')->group(function(){
                        Route::get('/', 'It\SettingsController@showProvinces')->name('settings-provinces');
                        Route::get('update-province-content', 'It\SettingsController@provinceContent')->name('province-content');
                        Route::get('/logs', 'It\SettingsController@showProvinceLogs')->name('settings-province-logs-details');
                        Route::prefix('cities')->group(function(){
                            Route::get('/', 'It\SettingsController@showCities')->name('settings-cities');
                            Route::get('update-city-content', 'It\SettingsController@cityContent')->name('city-content');
                            Route::get('/logs', 'It\SettingsController@showCityLogs')->name('settings-city-logs-details');
                            Route::prefix('barangays')->group(function(){
                                Route::get('/', 'It\SettingsController@showBarangays')->name('settings-barangays');
                                Route::get('update-barangay-content', 'It\SettingsController@barangayContent')->name('barangay-content');
                                Route::get('/logs', 'It\SettingsController@showBarangayLogs')->name('settings-barangay-logs-details');
                            });
                        });
                    });
                });
                Route::prefix('banks')->group(function(){
                    Route::get('/', 'It\SettingsController@showBanks')->name('settings-banks');
                    Route::get('update-bank-content', 'It\SettingsController@bankContent')->name('bank-content');
                    Route::get('/logs', 'It\SettingsController@showBankLogs')->name('settings-bank-logs-details');
                });
                Route::prefix('payees')->group(function(){
                    Route::get('/', 'It\SettingsController@showPayees')->name('settings-payees');
                    Route::get('update-payee-content', 'It\SettingsController@payeeContent')->name('payee-content');
                    Route::get('/logs', 'It\SettingsController@showPayeeLogs')->name('settings-payee-logs-details');
                });
                Route::prefix('vehicles')->group(function(){
                    Route::get('/', 'It\SettingsController@showVehicles')->name('settings-vehicles');
                    Route::get('update-vehicle-content', 'It\SettingsController@vehicleContent')->name('vehicle-content');
                    Route::get('/logs', 'It\SettingsController@showVehicleLogs')->name('settings-vehicle-logs-details');
                });
                Route::prefix('accounting-papers')->group(function(){
                    Route::get('/', 'It\SettingsController@showAccountingPapers')->name('settings-accounting-papers');
                    Route::get('update-vehicle-content', 'It\SettingsController@accountingPaperContent')->name('accounting-paper-content');
                    Route::get('/logs', 'It\SettingsController@showAccountingPaperLogs')->name('settings-accounting-paper-logs-details');
                });
                Route::prefix('accounting-titles')->group(function(){
                    Route::get('/', 'It\SettingsController@showAccountingTitles')->name('settings-accounting-titles');
                    Route::get('/particulars', 'It\SettingsController@showAccountingTitleParticulars')->name('settings-accounting-title-particulars');
                    Route::get('update-accounting-title-content', 'It\SettingsController@accountingTitleContent')->name('accounting-title-content');
                    Route::get('/logs', 'It\SettingsController@showAccountingTitleLogs')->name('settings-accounting-title-logs-details');
                });
                Route::prefix('employee-requirements')->group(function(){
                    Route::get('/', 'It\SettingsController@showEmployeeRequirements')->name('settings-employee-requirements');
                    Route::get('update-employee-requirement-content', 'It\SettingsController@employeeRequirementContent')->name('employee-requirement-content');
                    Route::get('/logs', 'It\SettingsController@showEmployeeRequirementLogs')->name('settings-employee-requirement-logs-details');
                });
                Route::prefix('terms-and-conditions')->group(function(){
                    Route::get('/', 'It\SettingsController@showTermsAndConditions')->name('settings-terms-and-conditions');
                });
                Route::prefix('payment-request-limitations')->group(function(){
                    Route::get('/', 'It\SettingsController@showPaymentRequestLimitations')->name('settings-payment-request-limitations');
                    Route::get('update-payment-request-limitation-content', 'It\SettingsController@paymentRequestLimitationContent')->name('payment-request-limitation-content');
                    Route::get('/logs', 'It\SettingsController@showPaymentRequestLimitationLogs')->name('settings-payment-request-limitation-logs-details');
                });
                Route::prefix('job-request-types')->group(function(){
                    Route::get('/', 'It\SettingsController@showJobRequestTypes')->name('settings-job-request-types');
                    Route::get('update-job-request-type-content', 'It\SettingsController@jobRequestTypeContent')->name('job-request-type-content');
                    Route::get('/logs', 'It\SettingsController@showJobRequestTypeLogs')->name('settings-job-request-type-logs-details');
                });

                Route::post('/{id}', 'It\SettingsController@showFunctions')->name('settings-functions');
            });
            Route::prefix('employees')->group(function(){
                Route::get('/add', 'It\EmployeeController@index')->name('employee-add');
                Route::get('/view', 'It\EmployeeController@view')->name('employee-view');
                Route::get('/update', 'It\EmployeeController@update')->name('employee-update');
                Route::get('/employee-account', 'It\EmployeeController@employeeAccounts')->name('employee-account');
                Route::get('/update-account', 'It\EmployeeController@updateAccount')->name('employee-update-account');
                Route::get('/employee-info', 'It\EmployeeController@employeeInfo')->name('employee-info');
                Route::get('/employee-erp-create', 'It\EmployeeController@erpCreateContent')->name('employee-erp-create');
                Route::post('/{id}', 'It\EmployeeController@showFunctions')->name('employee-functions');
            });
            Route::prefix('users')->group(function(){
                Route::get('/list', 'It\UserController@user_list')->name('user-list');
                Route::get('/profile', 'It\UserController@user_profile')->name('user-profile');
                Route::get('/control', 'It\UserController@controlContent')->name('user-control');
                Route::post('/{id}', 'It\UserController@showFunctions')->name('user-functions');
            });

            Route::prefix('suppliers')->group(function(){
                Route::get('/', 'It\SupplierController@showSuppliers')->name('suppliers');
                Route::get('update-supplier-content', 'It\SupplierController@supplierContent')->name('supplier-content');
                Route::get('/logs', 'It\SupplierController@showSupplierLogs')->name('supplier-logs-details');
                Route::prefix('supplier-products')->group(function(){
                    Route::get('/', 'It\SupplierController@showSupplierProducts')->name('supplier-products');
                    Route::get('update-supplier-product-content', 'It\SupplierController@supplierProductContent')->name('supplier-product-content');
                    Route::get('/logs', 'It\SupplierController@showSupplierProductLogs')->name('supplier-product-logs-details');
                });
                Route::post('/{id}', 'It\SupplierController@showFunctions')->name('supplier-functions');
            });
            Route::prefix('clients')->group(function(){
                Route::get('/', 'It\ClientController@showClients')->name('clients');
                Route::get('update-client-content', 'It\ClientController@clientContent')->name('client-content');
                Route::get('/logs', 'It\ClientController@showClientLogs')->name('client-logs-details');
                Route::prefix('company-branches')->group(function(){
                    Route::get('/', 'It\ClientController@showCompanyBranches')->name('client-company-branches');
                    Route::get('update-company-branch-content', 'It\ClientController@companyBranchContent')->name('client-branch-content');
                    Route::get('/logs', 'It\ClientController@showCompanyBranchLogs')->name('client-branch-logs-details');
                });

                Route::post('/{id}', 'It\ClientController@showFunctions')->name('client-functions');
            });
            Route::prefix('purchasing')->group(function(){
                Route::get('/', 'It\PurchaseOrderController@showIndex')->name('purchasing-list');
                Route::prefix('suppliers')->group(function(){
                    Route::get('/', 'It\PurchaseOrderController@showSuppliers')->name('purchasing-supplier-list');
                    Route::get('/create', 'It\PurchaseOrderController@showCreatePO')->name('supplier-create-p-o');
                    Route::get('/update', 'It\PurchaseOrderController@showUpdatePO')->name('supplier-update-p-o');
                });
                Route::get('/details', 'It\PurchaseOrderController@showPOdetails')->name('p-o-details');
                Route::prefix('pdf')->group(function(){
                    Route::get('/details', 'It\PurchaseOrderController@pdfPODetails')->name('pdf-p-o-details');
                });
                Route::post('/{id}', 'It\PurchaseOrderController@showFunctions')->name('purchasing-functions');
            });
			Route::prefix('payment-request')->group(function(){
                Route::get('/', 'It\PaymentRequestController@showIndex')->name('payment-request-list');
                Route::get('/create', 'It\PaymentRequestController@showCreate')->name('payment-request-create');
                Route::get('/update', 'It\PaymentRequestController@showUpdate')->name('payment-request-update');
                Route::get('/details', 'It\PaymentRequestController@showPaymentRequest')->name('payment-request-details');
                Route::prefix('pdf')->group(function(){
                    Route::get('/print-cheque', 'It\PaymentRequestController@showPrintCheque')->name('payment-request-pdf-print-cheque');
                });
                Route::post('/{id}', 'It\PaymentRequestController@showFunctions')->name('payment-request-functions');
            });
           
            Route::prefix('job_requests')->group(function(){
                Route::get('/list', 'It\JobRequestController@list')->name('job-request-list');
                Route::get('/view/{id}', 'It\JobRequestController@view')->name('job-request-view');
                Route::get('/image', 'It\JobRequestController@imageUpdate')->name('job-request-product-image');
                Route::post('/{id}', 'It\JobRequestController@showFunctions')->name('job-request-functions');
            });
        });
    }); 
    //SALES DEPARTMENT
    Route::group(['middleware' => 'department:SLS'], function(){
        Route::prefix('/sales-department')->group(function(){
            Route::get('/', 'Sales\DashboardController@showIndex')->name('sales-dashboard');
            Route::prefix('settings')->group(function(){
                Route::get('/profile', 'Sales\SettingsController@userProfile')->name('sales-settings-profile');
                Route::post('/{id}', 'Sales\SettingsController@showFunctions')->name('sales-settings-functions');
            });
            //start gelo added
            Route::prefix('job_requests')->group(function(){
                Route::get('/list', 'Sales\JobRequestController@list')->name('sales-job-request-list');
                Route::get('/view/{id}', 'Sales\JobRequestController@view')->name('sales-job-request-view');
                Route::get('/image', 'Sales\JobRequestController@imageUpdate')->name('sales-job-request-product-image');
                Route::post('/{id}', 'Sales\JobRequestController@showFunctions')->name('sales-job-request-functions');
                Route::get('/fp_image', 'Sales\JobRequestController@floorPlanImageUpdate')->name('sales-job-request-floor-plan-image');
                Route::get('/update-product-desc-content', 'Sales\JobRequestController@productDescContent')->name('sales-job-request-product-desc-content');
                Route::get('/update-jr-revision-content', 'Sales\JobRequestController@jrRevisionContent')->name('sales-job-request-revision-content');
            });
            //end gelo added
            Route::prefix('quotations')->group(function(){
                Route::get('/create', 'Sales\QuotationController@create')->name('sales-quotation-create');
                Route::get('/list', 'Sales\QuotationController@list')->name('sales-quotation-list');
                Route::get('/view', 'Sales\QuotationController@view')->name('sales-quotation-view');
                Route::get('/products', 'Sales\QuotationController@quotationProducts')->name('sales-quotation-products');
                Route::get('/preview', 'Sales\QuotationController@quotationPreview')->name('sales-quotation-preview');
                Route::get('/jr', 'Sales\QuotationController@jobrequestProductList')->name('sales-quotation-jobrequest');
                Route::get('/product-process','Sales\QuotationController@quotationProcess')->name('sales-quotation-product-process');
                Route::post('/{id}', 'Sales\QuotationController@showFunctions')->name('sales-quotation-functions');
            });	

            Route::prefix('products')->group(function(){
                Route::get('/', 'Sales\ProductController@showIndex')->name('sales-product-list');
                Route::get('/create', 'Sales\ProductController@showAddproduct')->name('sales-product-create');
                Route::get('/create-raw', 'Sales\ProductController@showAddproductRaw')->name('sales-product-create-raw');
                Route::get('/create-fit-out', 'Sales\ProductController@showAddproductFitOut')->name('sales-product-create-fit-out');
                Route::get('/update', 'Sales\ProductController@showUpdateproduct')->name('sales-product-update-details');
                Route::get('/logs', 'Sales\ProductController@showProductLogs')->name('sales-product-logs-details');
                Route::prefix('variants')->group(function(){
                    Route::get('/', 'Sales\ProductController@showVariants')->name('sales-product-variants');
                });
                Route::get('/swatch-details', 'Sales\ProductController@showSwatchDetails')->name('sales-product-swatch-details');
                Route::post('/{id}', 'Sales\ProductController@showFunctions')->name('sales-product-functions');
            });

            Route::prefix('suppliers')->group(function(){
                Route::get('/', 'Sales\SupplierController@showSuppliers')->name('sales-suppliers');
                Route::get('update-supplier-content', 'Sales\SupplierController@supplierContent')->name('sales-supplier-content');
                Route::get('/logs', 'Sales\SupplierController@showSupplierLogs')->name('sales-supplier-logs-details');
                Route::prefix('supplier-products')->group(function(){
                    Route::get('/', 'Sales\SupplierController@showSupplierProducts')->name('sales-supplier-products');
                    Route::get('update-supplier-product-content', 'Sales\SupplierController@supplierProductContent')->name('sales-supplier-product-content');
                    Route::get('/logs', 'Sales\SupplierController@showSupplierProductLogs')->name('sales-supplier-product-logs-details');
                });
                Route::post('/{id}', 'It\SupplierController@showFunctions')->name('sales-supplier-functions');
            });
            Route::prefix('clients')->group(function(){
                Route::get('/', 'Sales\ClientController@showClients')->name('sales-clients');
                Route::get('update-client-content', 'Sales\ClientController@clientContent')->name('sales-client-content');
                Route::get('/logs', 'Sales\ClientController@showClientLogs')->name('sales-client-logs-details');
                Route::prefix('company-branches')->group(function(){
                    Route::get('/', 'Sales\ClientController@showCompanyBranches')->name('sales-client-company-branches');
                    Route::get('update-company-branch-content', 'Sales\ClientController@companyBranchContent')->name('sales-client-branch-content');
                    Route::get('/logs', 'Sales\ClientController@showCompanyBranchLogs')->name('sales-client-branch-logs-details');
                });
                Route::post('/{id}', 'Sales\ClientController@showFunctions')->name('sales-client-functions');
            });
            Route::prefix('delivery_schedules')->group(function(){
                Route::get('/create', 'Sales\DeliveryScheduleController@create')->name('sales-delivery-create');
                Route::post('/{id}', 'Sales\DeliveryScheduleController@showFunctions')->name('sales-delivery-functions');
            });
        });
    });
    //ADMIN DEPARTMENT
    Route::group(['middleware' => 'department:ADMIN'], function(){
        Route::prefix('/admin-department')->group(function(){
            Route::get('/', 'Admin\DashboardController@showIndex')->name('admin-dashboard');
            Route::prefix('settings')->group(function(){
                Route::get('/profile', 'Admin\SettingsController@userProfile')->name('admin-settings-profile');
                Route::post('/{id}', 'Admin\SettingsController@showFunctions')->name('admin-settings-functions');
            });
        });
    });
    //HR DEPARTMENT
    Route::group(['middleware' => 'department:HR'], function(){
        Route::prefix('/hr-department')->group(function(){
            Route::get('/', 'Hr\DashboardController@showIndex')->name('hr-dashboard');
            Route::prefix('employees')->group(function(){
                Route::get('/add', 'Hr\EmployeeController@index')->name('hr-employee-add');
                Route::get('/view', 'Hr\EmployeeController@view')->name('hr-employee-view');
                Route::get('/update', 'Hr\EmployeeController@update')->name('hr-employee-update');
                Route::get('/employee-account', 'Hr\EmployeeController@employeeAccounts')->name('hr-employee-account');
                Route::get('/update-account', 'Hr\EmployeeController@updateAccount')->name('hr-employee-update-account');
                Route::get('/employee-info', 'Hr\EmployeeController@employeeInfo')->name('hr-employee-info');
                Route::get('/employee-erp-create', 'Hr\EmployeeController@erpCreateContent')->name('hr-employee-erp-create');
                Route::post('/{id}', 'Hr\EmployeeController@showFunctions')->name('hr-employee-functions');
            });
            Route::prefix('users')->group(function(){
                Route::get('/list', 'It\UserController@user_list')->name('hr-user-list');
                Route::get('/profile', 'It\UserController@user_profile')->name('hr-user-profile');
                Route::get('/control', 'It\UserController@controlContent')->name('hr-user-control');
                Route::post('/{id}', 'It\UserController@showFunctions')->name('hr-user-functions');
            });
            Route::prefix('settings')->group(function(){
                Route::prefix('departments')->group(function(){
                    Route::get('/', 'Hr\SettingsController@showDepartments')->name('hr-settings-departments');
                    Route::get('/logs', 'Hr\SettingsController@showDepartmentLogs')->name('hr-settings-department-logs-details');
                    Route::get('update-department-content', 'Hr\SettingsController@departmentContent')->name('hr-department-content');
                    Route::prefix('positions')->group(function(){
                        Route::get('/', 'Hr\SettingsController@showPositions')->name('hr-settings-positions');
                        Route::get('/logs', 'Hr\SettingsController@showPositionLogs')->name('hr-settings-position-logs-details');
                        Route::get('update-position-content', 'Hr\SettingsController@positionContent')->name('hr-position-content');
                    });
                });
                Route::post('/{id}', 'Hr\SettingsController@showFunctions')->name('hr-settings-functions');
            });			
        });
    });
    //ACCOUNTING DEPARTMENT
    Route::group(['middleware' => 'department:ACCTG'], function(){
        Route::prefix('/accounting-department')->group(function(){
            Route::get('/', 'Accounting\DashboardController@showIndex')->name('accounting-dashboard');
            Route::prefix('settings')->group(function(){
                Route::get('/profile', 'Accounting\SettingsController@userProfile')->name('accounting-settings-profile');
                Route::prefix('accounting-papers')->group(function(){
                    Route::get('/', 'Accounting\SettingsController@showAccountingPapers')->name('accounting-settings-accounting-papers');
                    Route::get('update-vehicle-content', 'Accounting\SettingsController@accountingPaperContent')->name('accounting-accounting-paper-content');
                    Route::get('/logs', 'Accounting\SettingsController@showAccountingPaperLogs')->name('accounting-settings-accounting-paper-logs-details');
                });
                Route::prefix('accounting-titles')->group(function(){
                    Route::get('/', 'Accounting\SettingsController@showAccountingTitles')->name('accounting-settings-accounting-titles');
                    Route::get('/particulars', 'Accounting\SettingsController@showAccountingTitleParticulars')->name('accounting-settings-accounting-title-particulars');
                    Route::get('update-accounting-title-content', 'Accounting\SettingsController@accountingTitleContent')->name('accounting-accounting-title-content');
                    Route::get('/logs', 'Accounting\SettingsController@showAccountingTitleLogs')->name('accounting-settings-accounting-title-logs-details');
                });
                Route::prefix('banks')->group(function(){
                    Route::get('/', 'Accounting\SettingsController@showBanks')->name('accounting-settings-banks');
                    Route::get('update-bank-content', 'Accounting\SettingsController@bankContent')->name('accounting-bank-content');
                    Route::get('/logs', 'Accounting\SettingsController@showBankLogs')->name('accounting-settings-bank-logs-details');
                });
                Route::prefix('payees')->group(function(){
                    Route::get('/', 'Accounting\SettingsController@showPayees')->name('accounting-settings-payees');
                    Route::get('update-payee-content', 'Accounting\SettingsController@payeeContent')->name('accounting-payee-content');
                    Route::get('/logs', 'Accounting\SettingsController@showPayeeLogs')->name('accounting-settings-payee-logs-details');
                });
                Route::post('/{id}', 'Accounting\SettingsController@showFunctions')->name('accounting-settings-functions');
            });
            Route::prefix('payment-request')->group(function(){
                Route::get('/', 'Accounting\PaymentRequestController@showIndex')->name('accounting-payment-request-list');
                Route::get('/create', 'Accounting\PaymentRequestController@showCreate')->name('accounting-payment-request-create');
                Route::get('/update', 'Accounting\PaymentRequestController@showUpdate')->name('accounting-payment-request-update');
                Route::get('/details', 'Accounting\PaymentRequestController@showPaymentRequest')->name('accounting-payment-request-details');
                Route::prefix('pdf')->group(function(){
                    Route::get('/print-cheque', 'Accounting\PaymentRequestController@showPrintCheque')->name('accounting-payment-request-pdf-print-cheque');
                });
                Route::post('/{id}', 'Accounting\PaymentRequestController@showFunctions')->name('accounting-payment-request-functions');
            });
			Route::prefix('liquidation')->group(function(){
                Route::get('/', 'Accounting\LiquidationController@showIndex')->name('accounting-liquidation-list');
                Route::get('/pr-details', 'Accounting\LiquidationController@showPrDetails')->name('accounting-liquidation-pr-details');
                Route::post('/{id}', 'Accounting\LiquidationController@showFunctions')->name('accounting-liquidation-functions');
            });
            Route::prefix('collection-papers')->group(function(){
                Route::get('/', 'Accounting\QuotationController@sales_invoice')->name('accounting-sales-invoice');
            });
            Route::prefix('collections')->group(function(){
                Route::get('/', 'Accounting\CollectionController@list')->name('accounting-collection-list');
                Route::get('/add-collection-schedule', 'Accounting\CollectionController@create_schedule')->name('accounting-create-collection-schedule');
                Route::get('/payment-mode', 'Accounting\CollectionController@showPaymentMode')->name('accounting-collection-payment-mode');
                Route::post('/{id}', 'Accounting\CollectionController@showFunctions')->name('accounting-collection-functions');
            });
            Route::prefix('quotations')->group(function(){
                Route::get('/view', 'Accounting\QuotationController@view')->name('accounting-quotation-view');
                Route::get('/list', 'Accounting\QuotationController@list')->name('accounting-quotation-list');
                Route::post('/{id}', 'Accounting\QuotationController@showFunctions')->name('accounting-quotation-functions');
            });
        });
    });
    //FITOUT DEPARTMENT
    Route::group(['middleware' => 'department:FTO'], function(){
        Route::prefix('/fitout-department')->group(function(){
            Route::get('/', 'Fitout\DashboardController@showIndex')->name('fitout-dashboard');
            Route::prefix('settings')->group(function(){
                Route::get('/profile', 'Fitout\SettingsController@userProfile')->name('fitout-settings-profile');
                Route::post('/{id}', 'Fitout\SettingsController@showFunctions')->name('fitout-settings-functions');
            });
        });
    });
    //PLANT DEPARTMENT
    Route::group(['middleware' => 'department:PLT'], function(){
        Route::prefix('/plant-department')->group(function(){
            Route::get('/', 'Plant\DashboardController@showIndex')->name('plant-dashboard');
            Route::prefix('settings')->group(function(){
                Route::get('/profile', 'Plant\SettingsController@userProfile')->name('plant-settings-profile');
                Route::post('/{id}', 'Plant\SettingsController@showFunctions')->name('plant-settings-functions');
            });
        });
    });
    //CUSTOMER SERVICE DEPARTMENT
    Route::group(['middleware' => 'department:CST'], function(){
        Route::prefix('/customer-service-department')->group(function(){
            Route::get('/', 'Cst\DashboardController@showIndex')->name('cst-dashboard');
            Route::prefix('settings')->group(function(){
                Route::get('/profile', 'Cst\SettingsController@userProfile')->name('cst-settings-profile');
                Route::post('/{id}', 'Cst\SettingsController@showFunctions')->name('cst-settings-functions');
            });
        });
    });
    //PRODUCTION DEPARTMENT
    Route::group(['middleware' => 'department:PROD'], function(){
        Route::prefix('/production-department')->group(function(){
            Route::get('/', 'Production\DashboardController@showIndex')->name('production-dashboard');
            Route::prefix('settings')->group(function(){
                Route::get('/profile', 'Production\SettingsController@userProfile')->name('production-settings-profile');
                Route::post('/{id}', 'Production\SettingsController@showFunctions')->name('production-settings-functions');
            });
        });
    });
    //WAREHOUSE (BICUTAN) DEPARTMENT
    Route::group(['middleware' => 'department:WHSE'], function(){
        Route::prefix('/warehouse-bicutan-department')->group(function(){
            Route::get('/', 'WarehouseBicutan\DashboardController@showIndex')->name('warehouse-bicutan-dashboard');
            Route::prefix('settings')->group(function(){
                Route::get('/profile', 'WarehouseBicutan\SettingsController@userProfile')->name('warehouse-bicutan-settings-profile');
                Route::post('/{id}', 'WarehouseBicutan\SettingsController@showFunctions')->name('warehouse-bicutan-settings-functions');
            });
        });
    });
    //MARKETING DEPARTMENT
    Route::group(['middleware' => 'department:MKTG'], function(){
        Route::prefix('/marketing-department')->group(function(){
            Route::get('/', 'Marketing\DashboardController@showIndex')->name('marketing-dashboard');
            Route::prefix('settings')->group(function(){
                Route::get('/profile', 'Marketing\SettingsController@userProfile')->name('marketing-settings-profile');
                Route::post('/{id}', 'Marketing\SettingsController@showFunctions')->name('marketing-settings-functions');
            });
        });
    });
    //DESIGN DEPARTMENT
    Route::group(['middleware' => 'department:DSGN'], function(){
        Route::prefix('/design-department')->group(function(){
            Route::get('/', 'Design\DashboardController@showIndex')->name('design-dashboard');
            Route::prefix('settings')->group(function(){
                Route::get('/profile', 'Design\SettingsController@userProfile')->name('design-settings-profile');
                Route::post('/{id}', 'Design\SettingsController@showFunctions')->name('design-settings-functions');
            });
            //start gelo added
            Route::prefix('job_requests')->group(function(){
                Route::get('/list', 'Design\JobRequestController@list')->name('design-job-request-list');
                Route::get('/view/{id}', 'Design\JobRequestController@view')->name('design-job-request-view');
                Route::get('/image', 'Design\JobRequestController@imageUpdate')->name('design-job-request-product-image');
                Route::post('/{id}', 'Design\JobRequestController@showFunctions')->name('design-job-request-functions');
                Route::get('/fp_image', 'Design\JobRequestController@floorPlanImageUpdate')->name('design-job-request-floor-plan-image');
            });
            //end gelo added
        });
    });
    //PROPRIETOR DEPARTMENT
    Route::group(['middleware' => 'department:PRPTR'], function(){
        Route::prefix('/proprietor-department')->group(function(){
            Route::get('/', 'Proprietor\DashboardController@showIndex')->name('proprietor-dashboard');
            Route::prefix('purchasing')->group(function(){
                Route::get('/', 'Proprietor\PurchaseOrderController@showIndex')->name('proprietor-purchasing-list');
                Route::get('/details', 'Proprietor\PurchaseOrderController@showPOdetails')->name('proprietor-p-o-details');
                Route::prefix('pdf')->group(function(){
                    Route::get('/details', 'Proprietor\PurchaseOrderController@pdfPODetails')->name('proprietor-pdf-p-o-details');
                });
                Route::post('/{id}', 'Proprietor\PurchaseOrderController@showFunctions')->name('proprietor-purchasing-functions');
            });
            Route::prefix('payment-request')->group(function(){
                Route::get('/', 'Proprietor\PaymentRequestController@showIndex')->name('proprietor-payment-request-list');
                Route::get('/details', 'Proprietor\PaymentRequestController@showPaymentRequest')->name('proprietor-payment-request-details');
                Route::post('/{id}', 'Proprietor\PaymentRequestController@showFunctions')->name('proprietor-payment-request-functions');
            });
            Route::prefix('quotations')->group(function(){
                Route::get('/list', 'Proprietor\QuotationController@list')->name('proprietor-quotation-list');
                Route::get('/view', 'Proprietor\QuotationController@view')->name('proprietor-quotation-view');
                Route::post('/{id}', 'Proprietor\QuotationController@showFunctions')->name('proprietor-quotation-functions');
            });	
            Route::prefix('settings')->group(function(){
                Route::get('/profile', 'Proprietor\SettingsController@userProfile')->name('proprietor-settings-profile');
                Route::post('/{id}', 'Proprietor\SettingsController@showFunctions')->name('proprietor-settings-functions');
            });
        });
    });
    //LOGISTICS DEPARTMENT
    Route::group(['middleware' => 'department:LGCS'], function(){
        Route::prefix('/logistics-department')->group(function(){
            Route::get('/', 'Logistics\DashboardController@showIndex')->name('logistics-dashboard');
            Route::prefix('settings')->group(function(){
                Route::get('/profile', 'Logistics\SettingsController@userProfile')->name('logistics-settings-profile');
                Route::post('/{id}', 'Logistics\SettingsController@showFunctions')->name('logistics-settings-functions');
            });
            Route::prefix('delivery-schedule')->group(function(){
                Route::get('/', 'Logistics\DeliveryScheduleController@list')->name('logistics-delivery-scehdule-list');
            });
        });
    });
    //LOGISTICS WAREHOUSE (RAW) DEPARTMENT
    Route::group(['middleware' => 'department:LGCS-WRM'], function(){
        Route::prefix('/logistics-warehouse-raw-department')->group(function(){
            Route::get('/', 'LogisticsWarehouseRaw\DashboardController@showIndex')->name('logistics-warehouse-raw-dashboard');
            Route::prefix('settings')->group(function(){
                Route::get('/profile', 'LogisticsWarehouseRaw\SettingsController@userProfile')->name('logistics-warehouse-raw-settings-profile');
                Route::post('/{id}', 'LogisticsWarehouseRaw\SettingsController@showFunctions')->name('logistics-warehouse-raw-settings-functions');
            });
        });
    });
    //LOGISTICS WAREHOUSE (SUPPLY) DEPARTMENT
    Route::group(['middleware' => 'department:LGCS-WSUPP'], function(){
        Route::prefix('/logistics-warehouse-supply-department')->group(function(){
            Route::get('/', 'LogisticsWarehouseSupply\DashboardController@showIndex')->name('logistics-warehouse-supply-dashboard');
            Route::prefix('settings')->group(function(){
                Route::get('/profile', 'LogisticsWarehouseSupply\SettingsController@userProfile')->name('logistics-warehouse-supply-settings-profile');
                Route::post('/{id}', 'LogisticsWarehouseSupply\SettingsController@showFunctions')->name('logistics-warehouse-supply-settings-functions');
            });
        });
    });
    //PURCHASING DEPARTMENT
    Route::group(['middleware' => 'department:PHR'], function(){
        Route::prefix('/purchasing-department')->group(function(){
            Route::get('/', 'Purchasing\DashboardController@showIndex')->name('purchasing-dashboard');
            Route::prefix('settings')->group(function(){
                Route::get('/profile', 'Purchasing\SettingsController@userProfile')->name('purchasing-settings-profile');
                Route::post('/{id}', 'Purchasing\SettingsController@showFunctions')->name('purchasing-settings-functions');
            });
        });
    });

	//PURCHASING RAW DEPARTMENT
Route::group(['middleware' => 'department:PUR-RM'], function(){
        Route::prefix('/purchasing-raw-department')->group(function(){
            Route::get('/', 'PurchasingRaw\DashboardController@showIndex')->name('purchasing-raw-dashboard');
            Route::prefix('products')->group(function(){
                Route::get('/', 'PurchasingRaw\ProductController@showIndex')->name('purchasing-raw-product-list');
                Route::get('/create', 'PurchasingRaw\ProductController@showAddproductRaw')->name('purchasing-raw-product-create');
                Route::get('/update', 'PurchasingRaw\ProductController@showUpdateproduct')->name('purchasing-raw-product-update-details');
                Route::get('/logs', 'PurchasingRaw\ProductController@showProductLogs')->name('purchasing-raw-product-logs-details');
                Route::post('/{id}', 'PurchasingRaw\ProductController@showFunctions')->name('purchasing-raw-product-functions');
            });
            Route::prefix('suppliers')->group(function(){
                Route::get('/', 'PurchasingRaw\SupplierController@showSuppliers')->name('purchasing-raw-suppliers');
                Route::get('update-supplier-content', 'PurchasingRaw\SupplierController@supplierContent')->name('purchasing-raw-supplier-content');
                Route::get('/logs', 'PurchasingRaw\SupplierController@showSupplierLogs')->name('purchasing-raw-supplier-logs-details');
                Route::prefix('supplier-products')->group(function(){
                    Route::get('/', 'PurchasingRaw\SupplierController@showSupplierProducts')->name('purchasing-raw-supplier-products');
                    Route::get('update-supplier-product-content', 'PurchasingRaw\SupplierController@supplierProductContent')->name('purchasing-raw-supplier-product-content');
                    Route::get('/logs', 'PurchasingRaw\SupplierController@showSupplierProductLogs')->name('purchasing-raw-supplier-product-logs-details');
                });
                Route::post('/{id}', 'PurchasingRaw\SupplierController@showFunctions')->name('purchasing-raw-supplier-functions');
            });
            Route::prefix('purchasing')->group(function(){
                Route::get('/', 'PurchasingRaw\PurchaseOrderController@showIndex')->name('purchasing-raw-list');
                Route::prefix('suppliers')->group(function(){
                    Route::get('/', 'PurchasingRaw\PurchaseOrderController@showSuppliers')->name('purchasing-raw-supplier-list');
                    Route::get('/create', 'PurchasingRaw\PurchaseOrderController@showCreatePO')->name('purchasing-raw-supplier-create-p-o');
                    Route::get('/update', 'PurchasingRaw\PurchaseOrderController@showUpdatePO')->name('purchasing-raw-supplier-update-p-o');
                });
                Route::get('/details', 'PurchasingRaw\PurchaseOrderController@showPOdetails')->name('purchasing-raw-p-o-details');
                Route::prefix('pdf')->group(function(){
                    Route::get('/details', 'PurchasingRaw\PurchaseOrderController@pdfPODetails')->name('purchasing-raw-pdf-p-o-details');
                });
                Route::post('/{id}', 'PurchasingRaw\PurchaseOrderController@showFunctions')->name('purchasing-raw-purchasing-functions');
            });
            Route::prefix('quotations')->group(function(){
                Route::get('/list', 'PurchasingRaw\QuotationController@list')->name('purchasing-raw-quotation-list');
                Route::post('/{id}', 'PurchasingRaw\QuotationController@showFunctions')->name('purchasing-raw-quotation-functions');
            });
            Route::prefix('payment-request')->group(function(){
                Route::get('/', 'PurchasingRaw\PaymentRequestController@showIndex')->name('purchasing-raw-payment-request-list');
                Route::get('/create', 'PurchasingRaw\PaymentRequestController@showCreate')->name('purchasing-raw-payment-request-create');
                Route::get('/update', 'PurchasingRaw\PaymentRequestController@showUpdate')->name('purchasing-raw-payment-request-update');
                Route::get('/details', 'PurchasingRaw\PaymentRequestController@showPaymentRequest')->name('purchasing-raw-payment-request-details');
                Route::prefix('pdf')->group(function(){
                    Route::get('/print-cheque', 'PurchasingRaw\PaymentRequestController@showPrintCheque')->name('purchasing-raw-payment-request-pdf-print-cheque');
                });
                Route::post('/{id}', 'PurchasingRaw\PaymentRequestController@showFunctions')->name('purchasing-raw-payment-request-functions');
            });
			Route::prefix('liquidation')->group(function(){
                Route::get('/', 'PurchasingRaw\LiquidationController@showIndex')->name('purchasing-raw-liquidation-list');
                Route::get('/pr-details', 'PurchasingRaw\LiquidationController@showPrDetails')->name('purchasing-raw-liquidation-pr-details');
                Route::post('/{id}', 'PurchasingRaw\LiquidationController@showFunctions')->name('purchasing-raw-liquidation-functions');
            });
            Route::prefix('settings')->group(function(){
                Route::get('/', 'PurchasingRaw\SettingsController@showProfile')->name('purchasing-raw-settings-profile');
                Route::post('/{id}', 'PurchasingRaw\SettingsController@showFunctions')->name('purchasing-raw-settings-functions');
            });
        });
    });
    //PURCHASING SUPPLY DEPARTMENT
	Route::group(['middleware' => 'department:PUR-SUPP'], function(){
        Route::prefix('/purchasing-supply-department')->group(function(){
            Route::get('/', 'PurchasingSupply\DashboardController@showIndex')->name('purchasing-supply-dashboard');
            Route::prefix('products')->group(function(){
                Route::get('/', 'PurchasingSupply\ProductController@showIndex')->name('purchasing-supply-product-list');
                Route::get('/create', 'PurchasingSupply\ProductController@showAddproduct')->name('purchasing-supply-product-create');
                Route::get('/swatch-details', 'PurchasingSupply\ProductController@showSwatchDetails')->name('purchasing-supply-product-swatch-details');
                Route::get('/update', 'PurchasingSupply\ProductController@showUpdateproduct')->name('purchasing-supply-product-update-details');
                Route::get('/logs', 'PurchasingSupply\ProductController@showProductLogs')->name('purchasing-supply-product-logs-details');
                Route::prefix('variants')->group(function(){
                    Route::get('/', 'PurchasingSupply\ProductController@showVariants')->name('purchasing-supply-product-variants');
                });
                Route::post('/{id}', 'PurchasingSupply\ProductController@showFunctions')->name('purchasing-supply-product-functions');
            });
            Route::prefix('suppliers')->group(function(){
                Route::get('/', 'PurchasingSupply\SupplierController@showSuppliers')->name('purchasing-supply-suppliers');
                Route::get('update-supplier-content', 'PurchasingSupply\SupplierController@supplierContent')->name('purchasing-supply-supplier-content');
                Route::get('/logs', 'PurchasingSupply\SupplierController@showSupplierLogs')->name('purchasing-supply-supplier-logs-details');
                Route::prefix('supplier-products')->group(function(){
                    Route::get('/', 'PurchasingSupply\SupplierController@showSupplierProducts')->name('purchasing-supply-supplier-products');
                    Route::get('update-supplier-product-content', 'PurchasingSupply\SupplierController@supplierProductContent')->name('purchasing-supply-supplier-product-content');
                    Route::get('/logs', 'PurchasingSupply\SupplierController@showSupplierProductLogs')->name('purchasing-supply-supplier-product-logs-details');
                });
                Route::post('/{id}', 'PurchasingSupply\SupplierController@showFunctions')->name('purchasing-supply-supplier-functions');
            });
            Route::prefix('quotations')->group(function(){
                Route::get('/list', 'PurchasingSupply\QuotationController@showIndex')->name('purchasing-supply-quotation-list');
                Route::post('/{id}', 'PurchasingSupply\QuotationController@showFunctions')->name('purchasing-supply-quotation-functions');
            });
            Route::prefix('purchasing')->group(function(){
                Route::get('/', 'PurchasingSupply\PurchaseOrderController@showIndex')->name('purchasing-supply-list');
                Route::prefix('suppliers')->group(function(){
                    Route::get('/', 'PurchasingSupply\PurchaseOrderController@showSuppliers')->name('purchasing-supply-supplier-list');
                    Route::get('/create', 'PurchasingSupply\PurchaseOrderController@showCreatePO')->name('purchasing-supply-supplier-create-p-o');
                    Route::get('/update', 'PurchasingSupply\PurchaseOrderController@showUpdatePO')->name('purchasing-supply-supplier-update-p-o');
                });
                Route::get('/added-quotation-qty', 'PurchasingSupply\PurchaseOrderController@showQuotationProductAdded')->name('purchasing-supply-quotation-product-added-qty');
                Route::get('/details', 'PurchasingSupply\PurchaseOrderController@showPOdetails')->name('purchasing-supply-p-o-details');
                Route::prefix('pdf')->group(function(){
                    Route::get('/details', 'PurchasingSupply\PurchaseOrderController@pdfPODetails')->name('purchasing-supply-pdf-p-o-details');
                });
                Route::post('/{id}', 'PurchasingSupply\PurchaseOrderController@showFunctions')->name('purchasing-supply-purchasing-functions');
            });
            Route::prefix('payment-request')->group(function(){
                Route::get('/', 'PurchasingSupply\PaymentRequestController@showIndex')->name('purchasing-supply-payment-request-list');
                Route::get('/create', 'PurchasingSupply\PaymentRequestController@showCreate')->name('purchasing-supply-payment-request-create');
                Route::get('/update', 'PurchasingSupply\PaymentRequestController@showUpdate')->name('purchasing-supply-payment-request-update');
                Route::get('/details', 'PurchasingSupply\PaymentRequestController@showPaymentRequest')->name('purchasing-supply-payment-request-details');
                Route::prefix('pdf')->group(function(){
                    Route::get('/print-cheque', 'PurchasingSupply\PaymentRequestController@showPrintCheque')->name('purchasing-supply-payment-request-pdf-print-cheque');
                });
                Route::post('/{id}', 'PurchasingSupply\PaymentRequestController@showFunctions')->name('purchasing-supply-payment-request-functions');
            });
			Route::prefix('liquidation')->group(function(){
                Route::get('/', 'PurchasingSupply\LiquidationController@showIndex')->name('purchasing-supply-liquidation-list');
                Route::get('/pr-details', 'PurchasingSupply\LiquidationController@showPrDetails')->name('purchasing-supply-liquidation-pr-details');
                Route::post('/{id}', 'PurchasingSupply\LiquidationController@showFunctions')->name('purchasing-supply-liquidation-functions');
            });
            Route::prefix('settings')->group(function(){
                Route::get('/', 'PurchasingSupply\SettingsController@showProfile')->name('purchasing-supply-settings-profile');
                Route::post('/{id}', 'PurchasingSupply\SettingsController@showFunctions')->name('purchasing-supply-settings-functions');
            });
        });
    });	
});












