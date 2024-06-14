import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import { provideHttpClient, withInterceptorsFromDi } from '@angular/common/http';
import { FormsModule } from '@angular/forms';
import { RouterModule, Routes } from '@angular/router';

import { AppComponent } from './app.component';
import { OrderComponent } from './order/order.component';
import { HeaderComponent } from './header/header.component';

const appRoutes: Routes = [
  { path: '', component: OrderComponent },
  // otras rutas aquí si es necesario
];

@NgModule({
  declarations: [
    AppComponent,
    OrderComponent,
    HeaderComponent  // Declara tus componentes aquí
  ],
  imports: [
    BrowserModule,
    FormsModule,
    RouterModule.forRoot(appRoutes)  // Asegúrate de importar RouterModule
  ],
  providers: [
    provideHttpClient(withInterceptorsFromDi())
  ],
  bootstrap: [AppComponent]
})
export class AppModule { }
