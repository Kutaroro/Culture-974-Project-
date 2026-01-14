import { startStimulusApp } from '@symfony/stimulus-bundle';
import CategoryController from './controllers/category/category_controller.js';
import CategoryCreateFormController from './controllers/category/category_create_form_controller.js';

const app = startStimulusApp();
// register any custom, 3rd party controllers here
app.register('category', CategoryController);
app.register('category-create-form', CategoryCreateFormController);
