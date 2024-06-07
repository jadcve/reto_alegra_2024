import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { OrderComponent } from './order/order.component';
import { KitchenComponent } from './kitchen/kitchen.component';
import { InventoryComponent } from './inventory/inventory.component';
import { HistoryComponent } from './history/history.component';

const routes: Routes = [
  { path: 'orders', component: OrderComponent },
  { path: 'kitchen', component: KitchenComponent },
  { path: 'inventory', component: InventoryComponent },
  { path: 'history', component: HistoryComponent },
  { path: '', redirectTo: '/orders', pathMatch: 'full' },
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
