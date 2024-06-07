import { Component } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';

@Component({
  selector: 'app-order',
  templateUrl: './order.component.html',
  styleUrls: ['./order.component.css']
})
export class OrderComponent {
  orderQuantity: number = 1;

  constructor(private http: HttpClient) {}

  onSubmit() {
    const apiUrl = 'http://gerente-web/api/orders/create';
    const headers = new HttpHeaders({
      'Content-Type': 'application/json',
      'x-api-key': '90b077b9-aa27-26719c-4b44-9814-f849ec1e0bfb.a3t6Xdm'
    });

    const body = { quantity: this.orderQuantity };

    this.http.post(apiUrl, body, { headers }).subscribe(
      response => {
        console.log('Order submitted successfully', response);
        alert('Order submitted successfully');
      },
      error => {
        console.error('Error submitting order', error);
        alert('Error submitting order');
      }
    );
  }
}
