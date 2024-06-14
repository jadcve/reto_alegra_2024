import { Component, OnInit } from '@angular/core';
import { HttpClient } from '@angular/common/http';

@Component({
  selector: 'app-order',
  templateUrl: './order.component.html',
  styleUrls: ['./order.component.css']
})
export class OrderComponent implements OnInit {
  message: string = '';

  constructor(private http: HttpClient) {}

  ngOnInit() {
    this.getMessageFromAPI();
  }

  getMessageFromAPI() {
    const apiUrl = 'https://jsonplaceholder.typicode.com/todos/1';

    this.http.get(apiUrl).subscribe(
      (response: any) => {
        console.log('Response received successfully', response);
        this.message = response.title;
      },
      error => {
        console.error('Error fetching message', error);
        this.message = 'Error fetching message';
      }
    );
  }
}
