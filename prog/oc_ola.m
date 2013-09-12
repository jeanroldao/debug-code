#import <stdio.h>
#import <Foundation/Foundation.h>

@interface Person: NSObject
{
    NSString * _name;
    int _age;
}

- (id) initWithName:(NSString*)name andAge:(int)age;

- (void) sayHello;
@end

@implementation Person: NSObject

- (id) initWithName:(NSString*)name andAge:(int)age
{
	self = [self init];
	_name = [name copy];
	_age = age;
	return self;
}

- (void) sayHello
{
	NSLog(@"Oi, %@ (%d)!", _name, _age);
}
@end

int main(void)
{
	Person * p = [[Person alloc] initWithName:@"Jean" andAge: 25];
	[p sayHello];
	
    return 0;
}